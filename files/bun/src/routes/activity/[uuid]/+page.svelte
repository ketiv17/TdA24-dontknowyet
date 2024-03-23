<script>
  import {page} from '$app/stores';
  import { onMount } from 'svelte';

  let data;
  let uuid;
  $: uuid = $page.params.uuid;


  onMount(async () => {
    let response = await fetch(`/api/activity/${uuid}`);
    if (response.ok) {
      data = await response.json();
      console.log(data);
    } else {
      page.set({status: response.status, error: {message: response.statusText}});
    }
  });
</script>

<ol class="breadcrumb text-xs p-6 px-24">
  <li class="crumb"><a href="/" class="anchor">Domů</a></li>
  <li class="crumb" aria-hidden>›</li>
  <li class="crumb">{uuid}</li>
</ol>