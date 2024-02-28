import {checkLogin} from '$lib/login.js';

export async function fetchauth({fetch}) {
  const response = await fetch('/api/login/auth');
  checkLogin(response);
}