import { encodedCredentials } from '$lib/server/apiPassword';

export async function post(request) {
  const { body } = request;

  const response = await fetch('/api/login/', {
    method: 'POST',
    body,
    headers: {
      'Authorization': `Basic ${encodedCredentials}`
    },
  });

  return {
    status: response.status,
    body: await response.text(),
    headers: response.headers,
  };
}