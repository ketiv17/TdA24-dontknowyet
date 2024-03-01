<script>
  import {onMount} from 'svelte';
  import {goto} from '$app/navigation';
  import Calendar from './Calendar.svelte';
  import Paginator from './pager.svelte';
  import {formatDate, fullName} from '$lib/string.js';

  export let data;
  export let form;
  console.log(data.calendar);

  let username = '';
  let password = '';
  async function login() {
    const res = await fetch('/api/login/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({username, password})
    });
    if (res.ok) {
      location.reload();
    } else {
      console.error('login failed');
    }
  }

  onMount( () => {
    if (form !== null && form.cookie) { // save the cookie
      document.cookie = form.cookie;
      // reload the page
      location.reload();
    }
  });

  let days = {};
  let page = 0;
  let pageDates = {};
  let month;
  let monthNames = ["Leden", "Únor", "Březen", "Duben", "Květen", "Červen", "Červenec", "Srpen", "Září", "Říjen", "Listopad", "Prosinec"];

  // the calendarData is in format {"yyyy-mm-dd":[{event1},{event2}],...}
  $: (page, data , fillDays());
  function fillDays() {
    if (!data.loggedIn) return;
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
      if (!data.calendar[dateStr]) {
        data.calendar[dateStr] = [];
      }
      days[dateStr] = data.calendar[dateStr];

      date.setDate(date.getDate() + 1); // Move to the next day
    }

    let fromDate = new Date(date);
    fromDate.setDate(fromDate.getDate() - 35); // Go back 35 days from the last date
    pageDates.from = fromDate.toISOString().split('T')[0];

    let toDate = new Date(date);
    toDate.setDate(toDate.getDate() - 1); // Go back 1 day from the last date
    pageDates.to = toDate.toISOString().split('T')[0];
  }
  async function logout() {
    await fetch ('/api/login/logout/');
    location.reload();
  }
</script>

<svelte:head>
  <title>Lektorská zóna - Teacher digital Agency</title>
</svelte:head>

<div class="flex flex-col items-center w-full">
  {#if data.loggedIn}
    <h4 class="h4 mt-4">{monthNames[month]}</h4>
    <h6 class="h6">{formatDate(pageDates.from)+" - "+formatDate(pageDates.to)}</h6>
    <div class="w-11/12">
      <Calendar calendarData={days} currentMonth={month} />
    </div>
    <Paginator bind:page={page} />
    <button class="btn variant-filled-tertiary" on:click={()=> logout()}>Odhlásit</button>
  {:else}
    <div>
      <h2 class="h2 pt-8">Login:</h2>
      <!-- <form> -->
        <label class="label m-2">
          <span>username:</span>
          <input class="input variant-filled-secondary focus:border-tertiary-500" bind:value={username} title="username" type="text" placeholder="jméno" />
        </label>
        <label class="label m-2">
          <span>password:</span>
          <input class="input variant-filled-secondary focus:border-tertiary-500" bind:value={password} title="password" type="password" placeholder="password" />
        </label>
        <button class="btn btn-md m-2 variant-filled-tertiary" on:click={() => login()}>Přihlásit!</button>
      <!-- </form> -->
    </div>
  {/if}
</div>