<script>
  import {popup, getModalStore} from '@skeletonlabs/skeleton';
  import {formatDate, shortenString} from '$lib/string.js';
  import Event from './event.svelte';
  export let calendarData = {};
  export let currentMonth = 0;
  let width = 0;

  const popupSetting = {
    event: 'hover',
	  target: 'popupDiv',
	  placement: 'bottom'
  };
  let popups = [];

  $: calendarData && createPopups();
  function createPopups() {
    let eventNum = 0;
    popups=[];
    for (let date in calendarData) {
      let day = calendarData[date];
      day.forEach(event => {
        event.num = eventNum; 
        let newPopup = {...popupSetting};
        newPopup.target = "popupDiv-"+event.num.toString();
        popups = [...popups, newPopup];
        eventNum++;
      })
    };
  }

  const modalStore = getModalStore();
  function openModal(event) {
    const modal = {
      type: 'alert',
      buttonTextCancel: 'Zavřít',
      title: event.description,
      body: event.guest_firstname+' '+event.guest_lastname+"<br>"+event.guest_email+"<br>"+event.guest_number+"<br>"+event.from.slice(11,16)+" - "+event.to.slice(11,16),
    };
    modalStore.trigger(modal);
  }
  

  // return a boolean if the date is in the current month
  function isCurrentMonth(date) {
    let d = new Date(date);
    return d.getMonth() === currentMonth;
  }
  function isCurrentDay(date) {
    let d = new Date(date);
    let today = new Date();
    return d.toISOString().split('T')[0] === today.toISOString().split('T')[0];
  }

  function myModal(event) {
		const c = { ref: Event , props: { event }};
		const modal = {
			type: 'component',
			component: c,
			title: event.guest_firstname+' '+event.guest_lastname,
			body: '',
		};
		modalStore.trigger(modal);
	}
</script>

<div class="grid grid-cols-7 justify-items-center border border-primary-500" bind:clientWidth={width}>
  {#each Object.keys(calendarData) as day}
    <div class="flex flex-col items-center border border-primary-500 w-full text-sm min-h-32">
      <span class="font-bold {isCurrentMonth(day) ? "" : "text-secondary-500"} badge {isCurrentDay(day) ? "variant-filled-secondary" : ""}">{formatDate(day).slice(0,7)}</span>
      {#if calendarData[day].length > 0}
        {#each calendarData[day] as event}
          <button class="badge variant-filled-tertiary [&>*]:pointer-events-none" use:popup={popups[event.num]} on:click={myModal(event)}>{shortenString(event.guest_firstname+' '+event.guest_lastname,width/7)}</button>
          <div class="variant-filled-primary rounded-lg p-2" data-popup="popupDiv-{event.num.toString()}">
            <div class="flex flex-col">
              {#each [event.description, event.guest_firstname+' '+event.guest_lastname, event.guest_email, event.guest_number, event.from.slice(11,16)+" - "+event.to.slice(11,16)] as info}
                <span>{info}</span>
              {/each}
            </div>
          </div>
        {/each}
      {/if}
    </div>
  {/each}
</div>
