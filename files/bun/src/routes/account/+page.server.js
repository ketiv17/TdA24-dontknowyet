export async function load({fetch}) {
  const response = await fetch('http://localhost/api/calendar/my');
  let data =await response.json();
  return {calendar: data};
}