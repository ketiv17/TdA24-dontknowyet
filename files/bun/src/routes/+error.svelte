<script>
  import {page} from '$app/stores';
</script>
<svelte:head>
  <title>{$page.status} - Teacher digital Agency</title>
</svelte:head>

<div class="flex flex-col items-center m-10">
  <h1 class="h1">{$page.status}<br>{$page.error.message}</h1>
  <img src="https://http.cat/{$page.status}" alt="error cat">
  <i>from: <a href="https://http.cat/status/{$page.status}">https://http.cat/status/{$page.status}</a></i>
</div>