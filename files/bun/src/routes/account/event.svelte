<script>

	import { getModalStore } from '@skeletonlabs/skeleton';

  export let event;

	// Props
	/** Exposes parent props to this component. */

	const modalStore = getModalStore();

	// Base Classes
</script>

<!-- @component This example creates an embedded video modal. -->

{#if $modalStore[0]}
	<p class="modal-example-form card p-4 w-modal shadow-xl space-y-4">
    {@html event.guest_firstname+' '+event.guest_lastname+"<br>"+event.description+"<br>"+event.guest_email+"<br>"+event.guest_number+"<br>"+event.from.slice(11,16)+" - "+event.to.slice(11,16)}
  </p>
{/if}