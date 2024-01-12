<script>
  import {onMount} from 'svelte';
  import {ProgressRadial, Avatar, Autocomplete, popup} from '@skeletonlabs/skeleton';

  // FETCH DATA
  onMount(() => {
    fetchData();
    fetchTags();
  });

  let data;
  let allTags;

  async function fetchData() {
    const response = await fetch('/api/lecturers');
    data = await response.json();
  }
  async function fetchTags() {
    const response = await fetch('/api/tags');
    allTags = await response.json();
    allTags.forEach(tag => {
      tag.selected = false;
    });
  }

  //put this into a seperate file later
  function contrast(colorhex) {
    colorhex = colorhex.replace("#", "");
    var r = parseInt(colorhex.substr(0,2),16);
    var g = parseInt(colorhex.substr(2,2),16);
    var b = parseInt(colorhex.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? '#333333' : 'white';
  }


  //structure of data
  // data [{"uuid":"2980","tags": [{"uuid": "someuuid", ...}, ...], ...}, ...]
  // tagFilter ["uuid":"someÚuidOfTag", ...]
    

  // FILTER FUNCTIONS
  $: data && tagFilter && (locationFilter || locationFilter === '') && filterData();
  let filtered = [];

  function filterData() {
    filtered = byLocation(byTags(data));
  }

  function byTags(toFilter) {
    if (tagFilter.length === 0) {
      return toFilter;
    }
    return toFilter.filter(lecturer => {
      if (lecturer.tags.length === 0) {
        return false;
      }
      return tagFilter.every(tag => lecturer.tags.map(t => t.uuid).includes(tag));
    });
  }

  function byLocation(toFilter) {
    console.log(locationFilter)
    if (locationFilter === "") {
      console.log('empty');
      return toFilter;
    }
    return toFilter.filter(lecturer => lecturer.location === locationFilter);
  }

  // FOR SELECTING
  let tagFilter = [];
  let locations = [];
  let locationFilter = "";
  $: data && getLocations();

  function getLocations() {
    locations = [];
    data.forEach(lecturer => {
      if (!locations.map(l => l.label).includes(lecturer.location)) {
        let option = {"label": lecturer.location, "value": lecturer.location}
        locations = [...locations, option];
      }
    });
  }

  function toggle(tag) {
    if (tag.selected) {
      tagFilter = tagFilter.filter(uuid => uuid !== tag.uuid);
    } else {
      tagFilter = [...tagFilter, tag.uuid];
    }
	  tag.selected = !tag.selected;
    return tag;
  }

  function onLocationSelect(event) {
    locationFilter = event.detail.label;
  }

  let popupSettings = {
  	event: 'focus-click',
  	target: 'popupAutocomplete',
  	placement: 'bottom',
  };
</script>

<p class="h1 text-center m-5 mt-20">Katalog Lektorů</p>
<main class="flex justify-center items-center flex-col">
  <h2 class="h2">Filtrování:</h2>
  <!-- location and price filter -->
  <div class="grid grid-cols-2 justify-items-center">
    <!-- location -->
    <div class="m-3 mx-6">
      {#if locations.length !== 0}
        <h5 class="h5">Lokací:</h5>
        <input
          class="input h-10 p-4 autocomplete variant-filled-secondary"
          type="search"
          name="autocomplete-search"
          bind:value={locationFilter}
          placeholder="Search..."
          use:popup={popupSettings}
        />
        <div data-popup="popupAutocomplete" class="variant-filled-secondary rounded-xl">
          <Autocomplete
            bind:input={locationFilter}
            options={locations}
            on:selection={onLocationSelect}
          />
        </div>
      {/if}
    </div>
    <!-- price -->
  </div>

  <!-- tag filter -->
  <h5 class="h5">tagy:</h5>
  {#if allTags}
    <div class="col-span-full text-center">
      {#each allTags as tag}
        <button class="badge btnAnimation text-sm rounded-full m-1 border-2" 
          on:click={() => {tag = toggle(tag)}}
          style="border-color: {tag.color}; {tag.selected ? 'background-color:'+tag.color :''}; color: {tag.selected ? contrast(tag.color) : '#333333'};"
        >
          {tag.name}
        </button>
      {/each}
    </div>
  {/if}
  
  <!-- lecturer cards -->
  <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 sm:w-10/12 m-5 justify-items-center">
    <hr class="col-span-full w-full" style="border-color: #333333;">
    {#if filtered.length !== 0}
      {#each filtered as lecturer}
        <a href="/lecturer/{lecturer.uuid}" class="card card-hover max-w-96 m-2 p-1 min-h-32 variant-ghost-surface rounded-2xl border-2 border-primary-500">
          <div class="card-header flex">
            <Avatar src={lecturer.picture_url} width="w-32" shadow="shadow-2xl" />
            <div class="mx-3">
              <h3 class="h3">{lecturer.title_before+' '+lecturer.first_name+' '+lecturer.middle_name+' '+lecturer.last_name+' '+lecturer.title_after}</h3>
              <h5 class="h5">{lecturer.location}</h5>
            </div>
          </div>
          <p class="text-lg m-1">{lecturer.claim}</p>
          {#if lecturer.tags !== null && lecturer.tags.length !== 0}
            {#each lecturer.tags as tag}
              <span class="badge text-sm rounded-full m-1" style="background-color: {tag.color}; color: {contrast(tag.color)};">{tag.name}</span>
            {/each}
          {/if}
        </a>
      {/each}
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="btn w-20 m-20"/>
    {/if}
  </div>
</main>

<style>
  .btnAnimation {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
  }
  .btnAnimation:hover {
    filter: brightness(1.15);
  }
  .btnAnimation:active {
    scale: 95%;
  }
</style>