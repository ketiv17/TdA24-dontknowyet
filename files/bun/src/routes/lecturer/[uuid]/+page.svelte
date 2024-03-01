<script>
  import {ProgressRadial, Avatar} from '@skeletonlabs/skeleton';
  import {page} from '$app/stores';
  import {fullName, null2string, formatDate} from '$lib/string.js'
  import { getToastStore } from '@skeletonlabs/skeleton';

  const toastStore = getToastStore();

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
    if (date === "") alert("Vyberte datum");
    const res = await fetch(`/api/calendar/free/`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({uuid, date})
    });
    if (res.ok) {
      avilableTimes = await res.json();
    } else {
      console.error('failed to get available times');
    }
  }

  let form = {
    guest_firstname: '',
    guest_lastname: '',
    guest_email: '',
    guest_number: '',
    time: '',
    description: '',
    agreement: false
  };

  let inProgress = false;
  let result = "";
  let details = "";
  async function reserve() {
    if (inProgress) return;
    if (!form.agreement) {
      showMissingApproveToast();
      return;
    
    }
    // check if all required fields are filled
    for (const [key, value] of Object.entries(form)) {
      if (key === "description") continue;
      if (value === "") {
        showMissingToast();
        return;
      }
    }
    inProgress = true;
    form.time = date.concat(" ", form.time);
    const res = await fetch(`/api/calendar/create/`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ lecturer_uuid: uuid, ...form})
    });
    if (res.ok) {
      result = "Rezervace úspěšná";
    } else {
      showFailureToast(res.text())
    }
    inProgress = false;
  }
  function reset () {
    inProgress = false;
    result = "";
    details = "";
  }

  function showMissingToast() {
    const t = {
      message: '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;"><h2><b>Chybějící údaje!</b></h2><p>Pole označené * jsou povinné</p></div>',
      background: 'variant-filled-tertiary',
      hideDismiss: true,
      timeout: 5000,
      classes: 'border-4 border-secondary-500'
    };
    toastStore.trigger(t);
  }
  function showSuccessToast() {
    const t = {
      message: '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;"><h2><b>Rezervace úspěšná!</b></h2><p>Rezervace byla úspěšně odeslána</p></div>',
      background: 'variant-filled-tertiary',
      hideDismiss: true,
      timeout: 5000,
      classes: 'border-4 border-secondary-500'
    };
    toastStore.trigger(t);
  }
  function showFailureToast(errorMessage) {
    const t = {
      message: '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;"><h2><b>Zkontrolujte si údaje!</b></h2><p>'+errorMessage+'</p></div>',
      background: 'variant-filled-tertiary',
      hideDismiss: true,
      timeout: 5000,
      classes: 'border-4 border-secondary-500'
    };
    toastStore.trigger(t);
  }
  function showMissingApproveToast(){
    const t = {
      message: '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center;"><h2><b>Souhlas s podmínkami!</b></h2><p>Musíte souhlasit s podmínkami</p></div>',
      background: 'variant-filled-tertiary',
      hideDismiss: true,
      timeout: 5000,
      classes: 'border-4 border-secondary-500'
    };
    toastStore.trigger(t);
  
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
      {#if inProgress || result !== ""}
        {#if result.length === 0}
          <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
        {:else}
          <div class="text-center">
            <h3 class="h3">{result}</h3>
            <p>{result === "Rezervace úspěšná" ? "" : details}</p>
            <button class="btn variant-filled-tertiary" on:click={()=>reset()}>Vyplňte znovu</button>
          </div>
        {/if}
      {:else}
        <div>
          <!-- <form class="modal-form"> -->
            <label class="label">
              <span>Jméno:*</span>
              <input class="input variant-filled-secondary" type="text" name="guest_firstname" placeholder="Jan" bind:value={form.guest_firstname} />
            </label>
            <label class="label">
              <span>Příjmení:*</span>
              <input class="input variant-filled-secondary" type="text" name="guest_lastname" placeholder="Novák" bind:value={form.guest_lastname} />
            </label>
            <label class="label">
              <span>Email:*</span>
              <input class="input variant-filled-secondary" type="email" name="guest_email" placeholder="novak@email.com" bind:value={form.guest_email} />
            </label>
            <label class="label">
              <span>Telefon:*</span>
              <input class="input variant-filled-secondary" type="tel" name="guest_number" placeholder="123 456 789" bind:value={form.guest_number} />
            </label>
            <label class="label">
              <span>Datum:*</span>
              <input class="input variant-filled-secondary" type="date" name="date" bind:value={date} />
            </label>
            <label class="label">
              <span>Čas:* {avilableTimes.date.length > 0 ? "(volné časy na: "+formatDate(avilableTimes.date)+")":""}</span>
              <div class="flex">
                <button class="btn variant-filled-tertiary mr-1" on:click={()=>getTimes()}>zjistit dostupné časy</button>
                <select class="input variant-filled-secondary" name="time" on:click={()=>getTimes()} bind:value={form.time}>
                  {#each avilableTimes.availableSlots as time}
                    <option value={time}>{time}</option>
                  {/each}
                </select>
              </div>
            </label>
            <label class="label">
              <span>Popis:</span>
              <input class="input variant-filled-secondary" type="text" name="description" placeholder="Vaše zpráva pro lektora" bind:value={form.description} />
            </label>
            <span class="flex mt-1">
              <input type="checkbox" name="agreement" bind:checked={form.agreement}>
              <span class="ml-1">Souhlasím se <a href="/legislativa" class="italic underline text-blue-500">zpracováním osobních údajů</a>*</span>
            </span>
            <span class="text-xs">* povinné</span><br>
            <button class="btn variant-filled-tertiary" on:click={()=>reserve()}>Rezervovat</button>
          <!-- </form> -->
        </div>
      {/if}
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
    {/if}
  </div>
</main>