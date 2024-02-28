//import {apiPassword} from '$lib/apiPassword'
import {lecturerCache} from '$lib/filterCache.js';

export async function load({fetch}) {
  let data = [];
  let cache;
  const unsub = lecturerCache.subscribe(value => cache = value);

  if (cache && cache.length !== 0) {
    data = cache;
    return;
  } else {
    const response = await fetch('/api/lecturers');
    data = await response.json();
    lecturerCache.set(data);
  }
  unsub();
  return {lecturers: data};
}