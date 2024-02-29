import { encodedCredentials} from "$lib/server/apiPassword";
import {lecturerCache} from '$lib/server/filterCache.js';

export async function load({fetch}) {
  let data = [];
  let cache;
  const unsub = lecturerCache.subscribe(value => cache = value);

  if (cache && cache.length !== 0) {
    data = cache;
  } else {
    const response = await fetch('http://localhost/api/lecturers', {
      headers: {
        'Authorization': `Basic ${encodedCredentials}`
      }
    });
    data = await response.json();
    lecturerCache.set(data);
  }
  unsub();

  return {lecturers: data};
}