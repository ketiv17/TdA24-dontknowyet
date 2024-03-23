<script>
  let form = {
    uuid: "",
    activityName: "",
    description: "",
    objectives: [""],
    classStructure: "",
    lengthMin: "",
    lengthMax: "",
    edLevel: [""],
    tools: [""],
    homePreparation: [""],
    instructions: [""],
    agenda: [""],
    links: [""],
    gallery: [""]
  };

  function addField(field) {
    form[field].push("");
  }

  function removeField(field, index) {
    form[field].splice(index, 1);
  }

  function submitForm() {
    console.log(form);
  }
</script>

<form class="modal-form" on:submit|preventDefault={submitForm}>
  <!-- ... other fields ... -->
  {#each Object.keys(form) as field (field)}
    {#if Array.isArray(form[field])}
      {#each form[field] as item, index (index)}
        <label class="label">
          <span>{field.charAt(0).toUpperCase() + field.slice(1)} {index + 1}:*</span>
          <input class="input variant-filled-secondary" type="text" bind:value={form[field][index]} />
          <button type="button" on:click={() => removeField(field, index)}>Remove</button>
        </label>
      {/each}
      <button type="button" on:click={() => addField(field)}>Add {field.charAt(0).toUpperCase() + field.slice(1)}</button>
    {/if}
  {/each}
  <button class="btn variant-filled-tertiary">Submit</button>
</form>
<style>
    input::placeholder {
    color: white;
    font-weight: 300;
  }
</style>