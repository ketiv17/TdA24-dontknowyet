<script>
  import {search} from '$lib/utils.js'
  import {onMount} from 'svelte';
  import {goto} from '$app/navigation'; // import the goto function
  import { get } from 'svelte/store';


  let input;

  onMount(async() => {
    input = get(search);
  });

  //function that stores the input value in the search store and redirects to the activities page
  function searchIt() {
    search.set(input);
    goto('/activity');
  }

</script>

<form class="label m-2 mb-0 flex flex-col items-center" on:submit|preventDefault={() => searchIt()}>
  <input class="input variant-filled-primary text-white focus:border-tertiary-500" bind:value={input} title="search" type="text" placeholder="aktivita pro střední školu s tématem ekonomiky" style="max-width: 30%; text-align: center;" />
  <div class="flex justify-center">
    <button class="btn btn-md m-2 variant-filled-tertiary" type="submit">Vyhledat</button>
  </div>
</form>
<style>
  input::placeholder {
    color: white;
    font-weight: 300;
  }
</style>