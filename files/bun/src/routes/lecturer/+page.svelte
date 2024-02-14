<script>
  import {onMount} from 'svelte';
  import {ProgressRadial, Autocomplete, popup, Paginator} from '@skeletonlabs/skeleton';
  import RangeSlider from 'svelte-range-slider-pips';
  import LecturerCard from '$lib/lecturerCard.svelte';

  let tagColor = "#FECB2E"; //color of thetags  in the filter, cards are updated based on theme

  // FETCH DATA
  onMount(() =>{
    fetchData();
  });

  let data = [];

  async function fetchData() {
    const response = await fetch('/api/lecturers');
    data = await response.json();
  }


  // FILTER FUNCTIONS
  $: { data, tagFilter, locationFilter, priceFilter, filterData() };
  let filtered = [];

  function filterData() {
    filtered = byPrice(byLocation(byTags(data)));
  }

  function byTags(toFilter) {
    if (tagFilter.length === 0) {
      return toFilter;
    }
    return toFilter.filter(lecturer => {
      if (lecturer.tags === null ||lecturer.tags.length === 0) {
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
  let allTags = [];
  let locationWidth;

  let tagFilter = [];
  let locationFilter = "";
  let priceFilter = [0,maxPrice];

  $: data && getLocations();
  $: data && getMaxPrice();
  $: data && getTags()

  const formatter = (price) => price+' Kč';
  function getMaxPrice() {
    data.forEach(lecturer => {
      if (lecturer.price_per_hour > maxPrice) {
        maxPrice = lecturer.price_per_hour;
      }
    });
    priceFilter = [priceFilter[0], maxPrice];
    filterData();
  }

  function getTags() {
    data.forEach(lecturer => {
      if (lecturer.tags === null) {
        return;
      }
      lecturer.tags.forEach(tag => {
        if (!allTags.map(t => t.uuid).includes(tag.uuid)) {
          allTags = [...allTags, tag];
        }
      });
    });
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

  // PAGINATION
  let filteredSliced;
  let paginationSettings = {
  	page: 0,
  	limit: 9,
  	size: data.length,
  	amounts: [3,9,18,100],
  }
  $: paginationSettings.size = data.length;
	$: filteredSliced = filtered.slice(
		paginationSettings.page * paginationSettings.limit,
		paginationSettings.page * paginationSettings.limit + paginationSettings.limit
	);
</script>

<main class="flex justify-center items-center flex-col">
  <h2 class="h2">Filtrování:</h2>
  <!-- location and price filter -->
  <div class="grid grid-cols-2 gap-10 justify-items-center max-sm:w-full p-1">
    <!-- location -->
    <div class="m-3 mx-6" bind:clientWidth={locationWidth}>
      {#if locations.length !== 0}
        <h5 class="h5">Lokací:</h5>
        <input
          class="input h-10 p-4 autocomplete variant-filled-secondary" type="search" name="autocomplete-search"
          bind:value={locationFilter} placeholder="Search..." use:popup={popupSettings}
        />
        <div data-popup="popupAutocomplete" class="variant-filled-secondary rounded-xl" style="width: {locationWidth}px;">
          <Autocomplete bind:input={locationFilter} options={locations} on:selection={onLocationSelect} />
        </div>
      {/if}
    </div>
    <!-- price -->
    <div class="m-3 mx-6 lg:w-72 md:w-56 w-full">
      <h5 class="h5">Cenou:</h5>
      <h6 class="h6">{priceFilter.toSorted((a,b) => a-b)[0]+' Kč/hod - '+priceFilter.toSorted((a,b) => a-b)[1]+' Kč/hod'}</h6>
      <RangeSlider {formatter} bind:values={priceFilter} min={0} max={maxPrice} float ariaLabels={["min price","max price"]}/>
    </div>
  </div>

  <!-- tag filter -->
  <h5 class="h5">tagy:</h5>
  {#if allTags}
    <div class="col-span-full text-center">
      {#each allTags as tag}
        <button class="badge btnAnimation text-sm rounded-full m-1 border-2" on:click={() => {tag = toggle(tag)}}
          style="border-color: {tagColor}; {tag.selected ? 'background-color:'+tagColor :''}; color: #333333;"
        >
          {tag.name}
        </button>
      {/each}
    </div>
  {/if}
  
  <!-- lecturer cards -->
  <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3 sm:w-10/12 m-5 justify-items-center">
    <hr class="col-span-full w-full" style="border-color: #333333;">
    {#if filteredSliced.length !== 0}
      {#each filteredSliced as lecturer}
        <LecturerCard lecturer={lecturer} selectedTags={allTags.filter((tag)=>tag.selected).map((tag)=>tag.uuid)} />
      {/each}
      <div class="col-span-full">
        <Paginator bind:settings={paginationSettings} controlVariant="variant-filled-tertiary" select="variant-filled-tertiary rounded-full p-1 pl-3" />
      </div>
    {:else if data.length !== 0}
      <h3 class="h3 text-center col-span-full">Nenašli jsme nikoho kdo by odpovídal vašim filtrům</h3>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="btn w-20 m-20 col-span-full"/>
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