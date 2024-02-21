<script>
  import {loggedIn, user, checkLogin} from '$lib/login.js';
  let username;
  let password;

  async function login () {
    const response = await fetch('/api/login/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({username, password})
    });
    checkLogin(response);
  }
</script>

<div class="flex flex-col w-full items-center">
  {#if $loggedIn}
    {#if $user && Object.keys($user).length > 0}
      <p class="p-2">You are logged in as {$user.name}!</p>
    {:else}
      <p class="p-2">You are logged in!</p>
    {/if}
  {:else}
    <h2 class="h2 pt-2">Login:</h2>
    <div>
      <label class="label m-2">
        <span>username:</span>
        <input class="input variant-filled-secondary focus:border-tertiary-500" bind:value={username} title="username" type="text" placeholder="jméno" />
      </label>
      <label class="label m-2">
        <span>password:</span>
        <input class="input variant-filled-secondary focus:border-tertiary-500" bind:value={password} title="password" type="password" placeholder="password" />
      </label>
    </div>
    <button class="btn btn-md m-2 variant-filled-tertiary" on:click={() => login()}>Login!</button>
  {/if}
</div>