import { loggedIn, user, uuid } from '$lib/login.js';

export async function checkLogin (response) {
  if (response.status === 200) {
    let resp = await response.json();
    uuid.set(resp.uuid);
    loggedIn.set(true);
    getData(resp.uuid);
    return;
  }
  loggedIn.set(false);
}

async function getData (uuid) {
  const response = await fetch('/api/lecturers/'+uuid);
  let resp = await response.json();
  user.set(resp);
}