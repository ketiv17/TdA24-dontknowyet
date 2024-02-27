<script>
	// Stores
	import { getModalStore } from '@skeletonlabs/skeleton';

	// Props
	/** Exposes parent props to this component. */
	export let parent;
  export let uuid;

	const modalStore = getModalStore();

  let times = ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00"];



	// Form Data
  let agreement = false;
	const formData = {
    uuid: uuid,
    lecturer_uuid: "",
    guest_firstname: "",
    guest_lastname: "",
    guest_email: "",
    guest_number: "",
    date: "",
    time: "",
    description: "",
	};

  let avilableTimes = {
    date: "",
    availableSlots: []
  }
  async function getTimes() {
    const response = await fetch(`/api/calendar/free/`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({date: formData.date})
    });
    let avilableTimes = await response.json();
  }

	// We've created a custom submit function to pass the response and close the modal.
	function onFormSubmit() {
		console.log(formData);
	}

	// Base Classes
	const cBase = 'card p-4 w-modal shadow-xl space-y-4';
	const cHeader = 'text-2xl font-bold';
	const cForm = 'border border-surface-500 p-4 space-y-4 rounded-container-token';
</script>

<!-- @component This example creates a simple form modal. -->

{#if $modalStore[0]}
	<div class="modal-example-form {cBase}">
		<header class={cHeader}>{$modalStore[0].title ?? '(title missing)'}</header>
		<article>{$modalStore[0].body ?? '(body missing)'}</article>
		<!-- Enable for debugging: -->
		<form class="modal-form {cForm}">
			<label class="label">
				<span>Jméno:</span>
				<input class="input variant-filled-secondary" type="text" bind:value={formData.guest_firstname} placeholder="Jan" />
			</label>
			<label class="label">
				<span>Příjmení:</span>
				<input class="input variant-filled-secondary" type="text" bind:value={formData.guest_lastname} placeholder="Novák" />
			</label>
			<label class="label">
				<span>Email:</span>
				<input class="input variant-filled-secondary" type="email" bind:value={formData.guest_email} placeholder="novak@email.com" />
			</label>
      <label class="label">
				<span>Telefon:</span>
				<input class="input variant-filled-secondary" type="tel" bind:value={formData.guest_number} placeholder="123 456 789" />
			</label>
      <label class="label">
        <span>Datum:</span>
        <input class="input variant-filled-secondary" type="date" bind:value={formData.date} />
      </label>
      <label class="label">
				<span>Popis:</span>
				<input class="input variant-filled-secondary" type="text" bind:value={formData.description} placeholder="Vaša zpráva pro lektora" />
			</label>
      <label class="label">
        <span>Čas: ({avilableTimes.date.lenght > 0 ? avilableTimes.date:""})</span>
        <div class="flex">
          <button class="btn variant-filled-tertiary mr-1" on:click={()=>getTimes()}>zjistit dostupné časy</button>
          <select class="input variant-filled-secondary" bind:value={formData.time}>
            {#each avilableTimes.availableSlots as time}
              <option value={time}>{time}</option>
            {/each}
          </select>
        </div>
      </label>
      <span class="flex">
        <input type="checkbox" bind:checked={agreement}>
        <span class="ml-1">Souhlasím se zpracováním osobních údajů</span>
      </span>

		</form>
		<footer class="modal-footer {parent.regionFooter}">
			<button class="btn variant-filled-tertiary" on:click={parent.onClose}>{parent.buttonTextCancel}</button>
			<button class="btn variant-filled-tertiary" on:click={onFormSubmit}>Submit Form</button>
		</footer>
	</div>
{/if}