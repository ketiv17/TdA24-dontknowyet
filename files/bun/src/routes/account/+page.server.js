import { encodedCredentials } from "$lib/server/apiPassword";
import {z} from 'zod';
import {superValidate} from 'sveltekit-superforms/server';

const zodSchema = z.object({
  username: z.string().min(1),
  password: z.string().min(1)
});

export async function load({request, fetch, event}) {
  //const form = await superValidate(event, zodSchema);
  let session = await request.headers.get('cookie');
  const check = await fetch('http://localhost/api/login/auth', {
    headers: {
      'Cookie': session,
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  if (check.ok) {
    let calendar = await getCalendar(session);
    // const user = getUser(check.json().uuid);
    console.log(calendar)
    return {calendar: calendar, loggedIn: true};
  }
  return {loggedIn: false, calendar: []};
}

async function getCalendar (session) {
  const response = await fetch('http://localhost/api/calendar/my', {
    headers: {
      'Cookie': session,
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  const rtn = await response.json();
  console.log(rtn);
  return rtn;
}

async function getUser (uuid) {
  const response = await fetch('http://localhost/api/lecturers/'+uuid, {
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    }
  });
  return await response.json();
}

// export const actions = {
//   login: async (event) => {
//     const data = await event.request.formData();
//     const username = data.get('username')
//     const password = data.get('password')
//     const response = await fetch('http://localhost/api/login/', {
//       method: 'POST',
//       headers: {
//         'Authorization': `Basic ${encodedCredentials}`
//       },
//       body: JSON.stringify({username, password})
//     });

//     if (response.ok) {
//       return {
//         cookie: response.headers.get('set-cookie'),
//       }
//     }
//   }
// }