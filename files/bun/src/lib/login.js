import {writable} from "svelte/store";

export const loggedIn = writable(false);
export const uuid = writable('');
export const user = writable({});