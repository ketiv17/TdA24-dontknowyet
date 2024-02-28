import { encodedCredentials } from "$lib/server/apiPassword";

export async function load({fetch}) {
  const response = await fetch('http://localhost/api/calendar/my', {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  let data =await response.json();
  return {calendar: data};
}