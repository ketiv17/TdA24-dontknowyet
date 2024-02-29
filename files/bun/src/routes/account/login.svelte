<script>
  import {loggedIn, user} from '$lib/login.js';
  import {fullName} from '$lib/string.js'
  import {ProgressRadial} from '@skeletonlabs/skeleton';
  let username;
  let password;
</script>

<div class="flex flex-col w-full items-center">
  {#if $loggedIn}
    {#if $user && Object.keys($user).length > 0}
      <h3 class="h3">{fullName($user)}</h3>
      <a class="btn btn-md m-2 variant-filled-tertiary" href="/account">Lektorská zóna</a>
      <button class="btn btn-md m-2 variant-filled-tertiary">Logout</button>
    {:else}
      <ProgressRadial value={undefined} stroke="50" track="stroke-tertiary-500/30" meter="stroke-tertiary-500" strokeLinecap="round" class="w-2 m-20 self-center"/>
    {/if}
  {:else}
    <form method="POST" action="?/login">
      <h2 class="h2 pt-2">Login:</h2>
      <div>
        <label class="label m-2">
          <span>username:</span>
          <input class="input variant-filled-secondary focus:border-tertiary-500" name="username" title="username" type="text" placeholder="jméno" />
        </label>
        <label class="label m-2">
          <span>password:</span>
          <input class="input variant-filled-secondary focus:border-tertiary-500"name="password" title="password" type="password" placeholder="password" />
        </label>
      </div>
      <button class="btn btn-md m-2 variant-filled-tertiary">Login!</button>
    </form>
  {/if}
</div>