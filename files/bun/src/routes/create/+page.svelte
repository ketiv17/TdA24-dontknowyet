<script>
  let form = {
    uuid: "e3b0c442-5b8b-47ba-9d8a-e4d3d2f3d974",
    activityName: "Grandfinále TdA",
    description: "Finále 24. ročníku soutěže ve vývoji webových aplikací",
    objectives: ["Vyzkoušet si komunikovat s fiktivním klientem.", "Naučit se řídit projekt."],
    classStructure: "Group",
    lengthMin: 500,
    lengthMax: 700,
    edLevel: ["secondarySchool", "highSchool"],
    tools: ["Notebook", "Plyšáček", "Energie v láhvi"],
    homePreparation: [{ title: "Naposlouchat dvouhodinový YouTube tutoriál o JS", warn: "Možná bude lepší se učit TS", note: "Ty s přízvukem jsou nejlepší" }],
    instructions: [{ title: "Přečíst si pořádně zadání", warn: "POŘÁDNĚ", note: "OpenAI dokumentace se taky může hodit" }],
    agenda: [{ duration: 600, title: "Programování", description: "Taky budeme chodit na klientské a mentorské schůzky" }],
    links: [{ title: "Tour de App", url: "https://tourdeapp.cz/" }],
    gallery: [{ title: "Fotky z průběhu", images: [{ lowRes: "https://picsum.photos/seed/a/200", highRes: "https://picsum.photos/seed/a/1200" }] }]
  };

  function addField(field, subfield = null) {
    if (subfield) {
      form[field][0][subfield].push("");
    } else {
      form[field].push("");
    }
    form = { ...form };
  }

  function removeField(field, index, subfield = null) {
    if (subfield) {
      form[field][0][subfield].splice(index, 1);
    } else {
      form[field].splice(index, 1);
    }
    form = { ...form };
  }

  function submitForm() {
    console.log(form);
  }
</script>

<form class="modal-form m-4" on:submit|preventDefault={submitForm}>
  <label>
    Jméno aktivity: <input bind:value={form.activityName} />
  </label>
  <label>
    Popis: <input bind:value={form.description} />
  </label>
  <label>
    struktura: <input bind:value={form.classStructure} />
  </label>
  <label>
    Jak dlouho aktivita trvá: <input type="number" bind:value={form.lengthMin} />
  </label>
  <label>
    <input type="number" bind:value={form.lengthMax} />
  </label>
  Cíle: (prosíme každý cíl do nového řádku)
  {#each form.objectives as objective, i (i)}
  <label>
    <input bind:value={form.objectives[i]} />
    <button on:click|preventDefault={() => removeField('objectives', i)}>Remove</button>
  </label>
{/each}
<button on:click|preventDefault={() => addField('objectives')}>Add Objective</button>
  <!-- Repeat the pattern above for other array fields -->
  <button type="submit">Submit</button>
</form>
<style>
    input::placeholder {
    color: white;
    font-weight: 300;
  }
  input {
    @apply variant-filled-primary text-white input;
  }
</style>