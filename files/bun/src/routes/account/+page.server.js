export async function load({fetch}) {
  const response = await fetch('/api/calendar/my');
  let data =await response.json();
  return {calendar: data};
}