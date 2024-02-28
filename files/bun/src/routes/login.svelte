<script>
  import {loggedIn, user} from '$lib/login.js';
  //import {checkLogin} from '$lib/server/login.js';
  import {fullName} from '$lib/string.js'
  import {ProgressRadial} from '@skeletonlabs/skeleton';
  let username;
  let password;

  async function login () {
    const response = await fetch('/proxyApi/login.js', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({username, password})
    });
    console.log(response);
    checkLogin(response);
  }
  async function logout () {
    await fetch('/api/login/logout/');
    loggedIn.set(false);
    user.set({});
  }
</script>

<div class="flex flex-col w-full items-center">
  {#if $loggedIn}
    {#if $user && Object.keys($user).length > 0}
      <h3 class="h3">{fullName($user)}</h3>
      <a class="btn btn-md m-2 variant-filled-tertiary" href="/account">Lektorská zóna</a>
      <button class="btn btn-md m-2 variant-filled-tertiary" on:click={() => logout()}>Logout</button>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-2 m-20 self-center"/>
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