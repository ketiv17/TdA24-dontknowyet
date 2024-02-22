<script>
  import {page} from '$app/stores';
</script>
<svelte:head>
  <title>{$page.status} - Teacher digital Agency</title>
</svelte:head>

<div class="flex justify-center m-10">
  <h1 class="h1">{$page.status}<br>{$page.error.message}</h1>
</div>