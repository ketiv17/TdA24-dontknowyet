import { loggedIn, user, uuid } from '$lib/login.js';
import { encodedCredentials } from "$lib/server/apiPassword";

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
  const response = await fetch('http://localhost/api/lecturers/'+uuid, {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  let resp = await response.json();
  user.set(resp);
}