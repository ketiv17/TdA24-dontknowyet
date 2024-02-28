//import {apiPassword} from '$lib/apiPassword'
//import {lecturerCache} from '$lib/filterCache.js';

export async function load() {
  let data = [];
  async function fetchData() {
    let cache;
    lecturerCache.subscribe(value => cache = value);

    if (cache && cache.length !== 0) {
      data = cache;
      return;
    } else {
      const response = await fetch('/api/lecturers');
      data = await response.json();
      lecturerCache.set(data);
    }
  }
  return {lecturers: data};
}