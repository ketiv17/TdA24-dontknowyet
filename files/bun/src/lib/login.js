import {writable, readable} from "svelte/store";

export const loggedIn = writable(false);
export const uuid = writable('');
export let user = {};

export async function checkLogin (response) {
  if (response.status === 200) {
    let resp = await response.json();
    uuid.set(resp.uuid);
    loggedIn.set(true);
    return;
  }
  loggedIn.set(false);
}

async function getData (uuid) {
  const response = await fetch('/api/lecturers/'+uuid);
  user = await response.json();
}