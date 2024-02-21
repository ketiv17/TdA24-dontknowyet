import {writable} from "svelte/store";

export const loggedIn = writable(false);
export const uuid = writable('');

export async function checkLogin (response) {
  if (response.status === 200) {
    let resp = await response.json();
    uuid.set(resp.uuid);
    loggedIn.set(true);
    return;
  }
  loggedIn.set(false);
}