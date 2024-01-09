<script>
  import Katalog from './lecturer/+page.svelte';
</script>
<svelte:head>
  <title>Teacher digital Agency</title>
</svelte:head>
<div class="flex justify-center">
  <div class="m-4 w-full md:w-10/12 lg:w-8/12">
    <h1 class="h1 text-center mt-16 mb-5">Teacher digital Agency</h1>
    <!--<p class="text-2xl text-center">Katalog špičkových odborníků z řad učitelů, lektorů a tutorů.</p>-->
    <h2 class="h2 mt-10">O nás</h2>
    <article class="md:text-2xl text-lg">
      Teacher digital Agency je mladá společnost zaměřená na vyhledávání špičkových odborníků z řad učitelů, lektorů a tutorů. Sestavujeme exkluzivní databázi těchto specialistů, abychom mohli efektivně propojovat klienty s ideálními odborníky dle jejich přání a specifických potřeb. Vaše vzdělávání je u nás v dobrých rukou.
    </article>
  </div>
</div>
<Katalog />