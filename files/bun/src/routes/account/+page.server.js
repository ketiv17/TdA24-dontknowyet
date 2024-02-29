import { encodedCredentials } from "$lib/server/apiPassword";
import {z} from 'zod';
import {superValidate} from 'sveltekit-superforms/server';

const zodSchema = z.object({
  username: z.string().min(1),
  password: z.string().min(1)
});

export async function load({request, fetch, event}) {
  //const form = await superValidate(event, zodSchema);
  const session = await request.headers.get('cookie');
  const check = await fetch('http://localhost/api/login/auth', {
    headers: {
      'Cookie': session,
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  if (check.ok) {
    let calendar = getCalendar(session);
    const user = getUser(check.json().uuid);
    console.log({calendar: calendar, loggedIn: true, user: user});
    return {calendar: calendar, loggedIn: true, user: user};
  }
  console.log({loggedIn: false, calendar: []});
  return {loggedIn: false, calendar: []};
}

async function getCalendar (session) {
  const response = await fetch('http://localhost/api/calendar/my', {
    headers: {
      'Cookie': session,
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  return await response.json();
}

async function getUser (uuid) {
  const response = await fetch('http://localhost/api/lecturers/'+uuid, {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  return await response.json();
}

export const actions = {
  login: async (event) => {
    console.log(encodedCredentials);
    const data = await event.request.formData();
    const username = data.get('username')
    const password = data.get('password')
    const session = await event.request.headers.get('cookie');
    const response = await fetch('http://localhost/api/login/', {
      method: 'POST',
      headers: {
        'Authorization': `Basic ${encodedCredentials}`
      },
      body: JSON.stringify({username, password})
    });
    console.log(response);
  }
}