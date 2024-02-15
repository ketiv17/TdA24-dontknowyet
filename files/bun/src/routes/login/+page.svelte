<script>
  let email;
  let password;
  function login () {
    console.log("email: "+email+", password: "+password);
  }
</script>

<svelte:head>
  <title>Login - Teacher digital Agency</title>
</svelte:head>

<div class="flex flex-col w-full items-center">
  <h2 class="h2 pt-10">Login:</h2>
  <div>
    <label class="label m-2">
      <span>e-mail:</span>
      <input class="input variant-filled-secondary" bind:value={email} title="email" type="email" placeholder="john@example.com" autocomplete="email" />
    </label>
    <label class="label m-2">
      <span>password:</span>
      <input class="input variant-filled-secondary" bind:value={password} title="password" type="password" placeholder="password" />
    </label>
  </div>
  <button class="btn btn-md m-2 variant-filled-secondary" on:click={() => login()}>Login!</button>
</div>