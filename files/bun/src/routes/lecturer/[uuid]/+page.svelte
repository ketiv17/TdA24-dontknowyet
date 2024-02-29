<script>
  import {ProgressRadial, Avatar} from '@skeletonlabs/skeleton';
  import {page} from '$app/stores';
  import {fullName, null2string} from '$lib/string.js'

  export let data;
  let uuid;
  $: uuid = $page.params.uuid;

  const czechFor = {
    "emails": "e-mail",
    "telephone_numbers": "telefon"
  };
  const uriof = {
    "emails": "mailto:",
    "telephone_numbers": "tel:"
  }

  let date = "";
  let avilableTimes = {date: "", availableSlots: []};
  async function getTimes() {
    console.log(JSON.stringify({uuid, date}));
    const res = await fetch(`/api/calendar/free/`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({uuid, date})
    });
    if (res.ok) {
      avilableTimes = await res.json();
      console.log(avilableTimes);
    } else {
      console.error('failed to get available times');
    }
  }
</script>

<svelte:head>
  {#if data.lecturer}  
    <title>{fullName(data.lecturer)} - Teacher digital Agency</title>
  {:else}
    <title>Teacher digital Agency</title>
  {/if}
</svelte:head>

<main class="flex justify-center">
  <div class="flex flex-col gap-y-5 xl:w-1/2 lg:w-8/12 md:w-10/12 w-full mt-12">
    <ol class="breadcrumb text-xs p-2">
      <li class="crumb"><a href="/" class="anchor">Domů</a></li>
      <li class="crumb" aria-hidden>›</li>
      <li class="crumb">{uuid}</li>
    </ol>
    {#if data.lecturer}
      {#if data.lecturer.picture_url !== null}
        <Avatar src="{data.lecturer.picture_url}" class="w-48 self-center m-8" shadow="shadow-2xl" />
      {/if}
      <h1 class="h1 text-center">{fullName(data.lecturer)}</h1>
      <h4 class="h4 text-center">{null2string(data.lecturer.location)}</h4>
      <h3 class="h3 text-center">{null2string(data.lecturer.claim)}</h3>
      <div class="w-full text-center m-2">
        {#if data.lecturer.tags !== null && data.lecturer.tags.length !== 0}
          {#each data.lecturer.tags as tag}
            <span class="badge text-sm rounded-full m-1 variant-filled-tertiary">{tag.name}</span>
          {/each}
        {/if}
      </div>
      <article class="blockquote not-italic m-2">{@html null2string(data.lecturer.bio)}</article>
      <div class="grid sm:grid-cols-2 mx-4">
        <div>Kontakty:
          <ul class="list-disc ml-4">
            {#each ["telephone_numbers", "emails"] as contact_type}
              {#if data.lecturer.contact[contact_type] !== null && data.lecturer.contact[contact_type].lenght !== 0}
                <li>{czechFor[contact_type]+":"}
                  {#each data.lecturer.contact[contact_type] as contact_value}
                    <a href="{uriof[contact_type]+contact_value}" class="badge rounded-full variant-ghost-secondary m-1">{contact_value}</a>
                  {/each}
                </li>
              {/if}
            {/each}
          </ul>
        </div>
        <div>
          <p>Cena za hodinu: {null2string(data.lecturer.price_per_hour)} Kč/hod</p>
        </div>
      </div>
      <hr class="w-full" style="border-color: #333333;">
      <h3 class="h2">Rezervace lekce:</h3>
      <div>
        <!-- <form class="modal-form"> -->
          <label class="label">
            <span>Jméno:</span>
            <input class="input variant-filled-secondary" type="text" name="guest_firstname" placeholder="Jan" />
          </label>
          <label class="label">
            <span>Příjmení:</span>
            <input class="input variant-filled-secondary" type="text" name="guest_lastname" placeholder="Novák" />
          </label>
          <label class="label">
            <span>Email:</span>
            <input class="input variant-filled-secondary" type="email" name="guest_email" placeholder="novak@email.com" />
          </label>
          <label class="label">
            <span>Telefon:</span>
            <input class="input variant-filled-secondary" type="tel" name="guest_number" placeholder="123 456 789" />
          </label>
          <label class="label">
            <span>Datum:</span>
            <input class="input variant-filled-secondary" type="date" name="date" bind:value={date}/>
          </label>
          <label class="label">
            <span>Popis:</span>
            <input class="input variant-filled-secondary" type="text" name="description" placeholder="Vaša zpráva pro lektora" />
          </label>
          <label class="label">
            <span>Čas: {avilableTimes.date.lenght > 0 ? "("+avilableTimes.date+")":""}</span>
            <div class="flex">
              <button class="btn variant-filled-tertiary mr-1" on:click={()=>getTimes()}>zjistit dostupné časy</button>
              <select class="input variant-filled-secondary" name="time">
                {#each avilableTimes.availableSlots as time}
                  <option value={time}>{time}</option>
                {/each}
              </select>
            </div>
          </label>
          <span class="flex">
            <input type="checkbox" name="agreement">
            <span class="ml-1">Souhlasím se zpracováním osobních údajů</span>
          </span>
          <button class="btn variant-filled-tertiary" formaction="?/reserve">Rezervovat</button>
        <!-- </form> -->
      </div>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
    {/if}
  </div>
</main>