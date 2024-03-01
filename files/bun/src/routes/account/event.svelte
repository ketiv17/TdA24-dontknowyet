<script>
	import { getModalStore } from '@skeletonlabs/skeleton';
  import {null2string} from '$lib/string';

  export let parent;
  export let event;

	const modalStore = getModalStore();
</script>

<!-- @component This example creates an embedded video modal. -->

{#if $modalStore[0]}
	<div class="modal-example-form card p-4 w-modal shadow-xl space-y-4 flex flex-col">
    <h1 class="h1">{event.guest_firstname+" "+event.guest_lastname}</h1>
    <p>popis: {null2string(event.description)}</p>
    <p>e-mail: <a  class="badge rounded-full variant-ghost-secondary m-1" href="mailto:{event.guest_email}">{event.guest_email}</a></p>
    <p>telefon: <a class="badge rounded-full variant-ghost-secondary m-1" href="tel:{event.guest_number}">{event.guest_number}</a></p>
    <p>od - do: {event.from.slice(11,16)+" - "+event.to.slice(11,16)}</p>
    <button on:click={parent.onClose} class="btn variant-filled-tertiary w-32">Close</button>
  </div>
{/if}