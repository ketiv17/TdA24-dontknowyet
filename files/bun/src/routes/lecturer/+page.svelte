<script>
  import {onMount} from 'svelte';
  import {ProgressRadial, Autocomplete, popup} from '@skeletonlabs/skeleton';
  import RangeSlider from 'svelte-range-slider-pips';
  import LecturerCard from '$lib/lecturerCard.svelte';
  import {textContrast} from '$lib/index.js';

  // FETCH DATA
  onMount(() => {
    fetchData();
    fetchTags();
    });

  let data = [];
  let allTags = [];

  async function fetchData() {
    const response = await fetch('/api/lecturers/');
    data = await response.json();
  }

  async function fetchTags() {
    const response = await fetch('/api/tags/');
    allTags = await response.json();
    allTags.forEach(tag => {
      tag.selected = false;
    });
  }

  // FILTER FUNCTIONS
  $: data && tagFilter && (locationFilter || locationFilter === '') && priceFilter && filterData();
  let filtered = [];

  function filterData() {
    filtered = byPrice(byLocation(byTags(data)));
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
    if (locationFilter === "") {
      return toFilter;
    }
    return toFilter.filter(lecturer => lecturer.location === locationFilter);
  }

  function byPrice(toFilter) {
    return toFilter.filter(lecturer => priceFilter.toSorted((a,b) => a-b)[0] <= lecturer.price_per_hour
    && lecturer.price_per_hour <= priceFilter.toSorted((a,b) => a-b)[1]);
  }

  // FOR SELECTING
  let maxPrice = 0;
  let locations = [];

  let tagFilter = [];
  let locationFilter = "";
  let priceFilter = [0,maxPrice];

  $: data && getLocations();
  $: data && getMaxPrice();

  const formatter = (price) => price+' Kč';
  function getMaxPrice() {
    data.forEach(lecturer => {
      if (lecturer.price_per_hour > maxPrice){
        maxPrice = lecturer.price_per_hour;
      }
    });
    priceFilter = [priceFilter[0], maxPrice];
    filterData();
  }

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
  <div class="grid grid-cols-2 gap-10 justify-items-center">
    <!-- location -->
    <div class="m-3 mx-6">
      {#if locations.length !== 0}
        <h5 class="h5">Lokací:</h5>
        <input
          class="input h-10 p-4 autocomplete variant-filled-secondary" type="search" name="autocomplete-search"
          bind:value={locationFilter} placeholder="Search..." use:popup={popupSettings}
        />
        <div data-popup="popupAutocomplete" class="variant-filled-secondary rounded-xl">
          <Autocomplete bind:input={locationFilter} options={locations} on:selection={onLocationSelect} />
        </div>
      {/if}
    </div>
    <!-- price -->
    <div class="m-3 mx-6 w-72">
      <h5 class="h5">Cenou:{'  '+priceFilter.toSorted((a,b) => a-b)[0]+'Kč/hod - '+priceFilter.toSorted((a,b) => a-b)[1]+'Kč/hod'}</h5>
      <RangeSlider {formatter} bind:values={priceFilter} min={0} max={maxPrice} float/>
    </div>
  </div>

  <!-- tag filter -->
  <h5 class="h5">tagy:</h5>
  {#if allTags}
    <div class="col-span-full text-center">
      {#each allTags as tag}
        <button class="badge btnAnimation text-sm rounded-full m-1 border-2" on:click={() => {tag = toggle(tag)}}
          style="border-color: {tag.color}; {tag.selected ? 'background-color:'+tag.color :''}; color: {tag.selected ? textContrast(tag.color) : '#333333'};"
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
        <LecturerCard lecturer={lecturer} />
      {/each}
    {:else if data.length !== 0}
      <h3 class="h3 text-center col-span-full">Nenašli jsme nikoho kdo by odpovídal vašim filtrům</h3>
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