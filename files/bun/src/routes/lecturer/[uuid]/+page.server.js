import { encodedCredentials} from "$lib/server/apiPassword";

export async function load({fetch, url}) {
  const uuid = url.pathname.slice(10);
  const response = await fetch('http://localhost/api/lecturers/'+uuid, {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  const data = await response.json();
  console.log(data);
  return {lecturer: data};
}
