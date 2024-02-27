<script>
  import {onMount} from 'svelte';
  import {goto} from '$app/navigation';
  import {loggedIn, user} from '$lib/login.js';
  import Calendar from './Calendar.svelte';
  import Paginator from './pager.svelte';
  import {formatDate, fullName} from '$lib/string.js';

  let data = [];

  onMount( () => {
    fetchCalendar();
    const unsub = loggedIn.subscribe(value => {
      if (!value) {
        goto('/');
      }
    });
    return unsub; // unsubscribe when the component is unmounted
  });
  async function fetchCalendar() {
    const response = await fetch('/api/calendar/my');
    data = await response.json();
  }

  let days = {};
  let page = 0;
  let pageDates = {};
  let month;
  let monthNames = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];

  // the calendarData is in format {"yyyy-mm-dd":[{event1},{event2}],...}
  $: (page, data , fillDays());
  function fillDays() {
    let date = new Date();
    date.setMonth(date.getMonth() + page); // Set the month based on the page
    date.setDate(1); // Start from the first day of the month

    month = date.getMonth();

    // Adjust to the next Monday if the first day of the month is not Monday
    while (date.getDay() !== 1) {
      date.setDate(date.getDate() - 1);
    }

    days = {}; // Clear the days object
    for (let i = 0; i < 35; i++) { // Iterate 35 times for 5 weeks
      let dateStr = date.toISOString().split('T')[0];
      if (!data[dateStr]) {
        data[dateStr] = [];
      }
      days[dateStr] = data[dateStr];

      date.setDate(date.getDate() + 1); // Move to the next day
    }

    let fromDate = new Date(date);
    fromDate.setDate(fromDate.getDate() - 35); // Go back 35 days from the last date
    pageDates.from = fromDate.toISOString().split('T')[0];

    let toDate = new Date(date);
    toDate.setDate(toDate.getDate() - 1); // Go back 1 day from the last date
    pageDates.to = toDate.toISOString().split('T')[0];
  }
</script>

<svelte:head>
  <title>Lektorská zóna - Teacher digital Agency</title>
</svelte:head>

<div class="flex flex-col items-center w-full">
  <h1 class="h1 pt-8">{fullName($user)}</h1>
  <h2 class="h2 pt-5">Kalendář</h2>
  <h4 class="h4">{monthNames[month]}</h4>
  <h6 class="h6">{formatDate(pageDates.from)+" - "+formatDate(pageDates.to)}</h6>
  <div class="w-11/12">
    <Calendar calendarData={days} currentMonth={month} />
  </div>
  <Paginator bind:page={page} />
</div>
<!-- <Paginator bind:settings={paginationSettings} controlVariant="variant-filled-tertiary" select="variant-filled-tertiary border-none rounded-full p-1 pr-5 pl-3" /> -->