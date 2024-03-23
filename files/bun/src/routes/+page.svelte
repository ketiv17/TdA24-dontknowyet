<script>
  import Katalog from "./Katalog.svelte";
</script>
<svelte:head>
  <title>AMOS - Teacher digital Agency</title>
</svelte:head>
<div class="flex flex-col items-center p-3">
  <div class="m-4 w-full md:w-10/12 lg:w-8/12 mb-10">
      <h1 class="h1 text-center mt-16 mb-5 md:text-7xl text-5xl">AMOS - Teacher digital Agency</h1>
      <!--<p class="text-2xl text-center">Katalog špičkových odborníků z řad učitelů, lektorů a tutorů.</p>-->
      <h2 class="h2 mt-10">O nás</h2>
      <article class="md:text-2xl text-lg">
          amos je katalog aktivit pro lektory
      </article>
  </div>
  <hr class="w-10/12" style="border-color: #333333;">
</div>
<p class="h1 text-center m-5 mt-12">Vyhledávání aktivit</p>
<Katalog />
