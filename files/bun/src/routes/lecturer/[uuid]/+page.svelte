<script>
  import {onMount} from 'svelte';
  import {ProgressRadial} from '@skeletonlabs/skeleton';
  import {Avatar} from '@skeletonlabs/skeleton';
  import {clipboard} from '@skeletonlabs/skeleton';
  import {getToastStore} from '@skeletonlabs/skeleton';
  const toastStore = getToastStore();
  import {page} from '$app/stores';

  let data;
  let uuid;
  $: uuid = $page.params.uuid;

  async function fetchData() {
    const response = await fetch('/api/lecturers/'+uuid);
    data = await response.json();
  }

  onMount(() => {
    fetchData();
  });

  const t = {
    message: 'Hello',
  };

  function contrast(colorhex) {
    colorhex = colorhex.replace("#", "");
    var r = parseInt(colorhex.substr(0,2),16);
    var g = parseInt(colorhex.substr(2,2),16);
    var b = parseInt(colorhex.substr(4,2),16);
    var yiq = ((r*299)+(g*587)+(b*114))/1000;
    return (yiq >= 128) ? 'black' : 'white';
  };

  const czechFor = {
    "emails": "e-mail",
    "telephone_numbers": "telefon"
  };
</script>

<main class="flex justify-center">
  <div class="flex flex-col gap-y-5 lg:w-8/12 md:w-10/12 w-full mt-12">
    {#if data}
      <Avatar src="{data.picture_url}" class="w-48 self-center m-8" shadow="shadow-2xl" />
      <h1 class="h1 text-center">{data.title_before+' '+data.first_name+' '+data.middle_name+' '+data.last_name+' '+data.title_after}</h1>
      <h4 class="h4 text-center">{data.location}</h4>
      <h3 class="h3 text-center">{data.claim}</h3>
      <div class="w-full text-center m-2">
        {#each data.tags as tag}
          <span class="badge text-sm rounded-full m-1" style="background-color: {tag.color}; color: {contrast(tag.color)};">{tag.name}</span>
        {/each}
      </div>
      <article class="blockquote m-2">{@html data.bio}</article>
      <div class="grid sm:grid-cols-2 mx-4">
        <div>Kontakty:
          <ul class="list-disc ml-4">
            {#each ["telephone_numbers", "emails"] as contact_type}
              {#if data.contact[contact_type].length !== 0}
                <li>{czechFor[contact_type]+":"}
                  {#each data.contact[contact_type] as contact_value}
                    <button use:clipboard={contact_value} class="badge card-hover rounded-full variant-ghost-secondary m-1">{contact_value}</button>
                  {/each}
                </li>
              {/if}
            {/each}
          </ul>
        </div>
        <p>Cena za hodinu: {data.price_per_hour}</p>
      </div>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20 self-center"/>
    {/if}
  </div>
</main>