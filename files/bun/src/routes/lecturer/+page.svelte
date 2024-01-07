<script>
    import {onMount} from 'svelte';
    import {ProgressRadial} from '@skeletonlabs/skeleton'
    import {Avatar} from '@skeletonlabs/skeleton'
    let data;

    async function fetchData() {
        const response = await fetch('/api/lecturers/');
        data = await response.json();
    }
    onMount(() => {
        fetchData();
    });
</script>
<p class="h1 text-center m-10 mt-20">Katalog</p>
<main class="flex justify-center">
    <div class="grid grid-cols-2 min-w-10/12">
        {#if data}
            {#each data as lecturer}
                <a href="/lecturer/{lecturer.uuid}" class="card card-hover w-96 m-10 min-h-32 variant-ghost-surface rounded-2xl">
                    <div class="card-header flex">
                        <Avatar src={lecturer.picture_url} width="w-32" shadow="shadow-2xl" />
                        <p class="h3 m-4">{lecturer.title_before+' '+lecturer.first_name+' '+lecturer.middle_name+' '+lecturer.last_name+' '+lecturer.title_after}</p>
                    </div>
                    <p class="text-lg m-2">{lecturer.claim}</p>
                    <div class="m-1">
                        {#each lecturer.tags as tag}
                            <span class="badge variant-ghost-tertiary text-sm rounded-full m-1">{tag.name}</span>
                        {/each}
                    </div>
                </a>
            {/each}
        {:else}
            <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-20 m-20"/>
        {/if}
    </div>
</main>
