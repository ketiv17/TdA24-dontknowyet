import {checkLogin} from '$lib/server/login.js';

export async function load({fetch}) {
  const response = await fetch('http://localhost/api/login/auth');
  checkLogin(response);
}