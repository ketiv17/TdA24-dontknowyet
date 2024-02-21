<script>
  import {uuid} from '$lib/loginStore.js';
  let username;
  let password;
  async function login () {
    await fetch('/api/login/', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({username, password})
    });
    let tempUuid = await response.json();
    uuid.set(tempUuid);
  }
</script>

<div class="flex flex-col w-full items-center">
  <h2 class="h2 pt-2">Login:</h2>
  <p>{uuid}</p>
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
</div>