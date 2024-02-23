<script>
  import {onMount} from 'svelte';
  import {goto} from '$app/navigation';
  import {loggedIn, user} from '$lib/login.js';
  import Calendar from './Calendar.svelte';

  let data = [];

  onMount(async () => {
    fetchCalendar();
    const unsub = loggedIn.subscribe(value => {
      if (!value) {
        goto('/');
      }
    });

    return unsub; // unsubscribe when the component is unmounted
  });
  
  async function fetchCalendar() {
    const response = await fetch('/api/calendar/my/');
    data = await response.json();
    console.log(data);
  }
</script>

<svelte:head>
  <title>Lektorská zóna - Teacher digital Agency</title>
</svelte:head>

<Calendar calendarData={data}/>