import { encodedCredentials} from "$lib/server/apiPassword";

export async function load({fetch}) {
  let data = [];

  const response = await fetch('http://localhost/api/lecturers', {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  data = await response.json();

  return {lecturers: data};
}