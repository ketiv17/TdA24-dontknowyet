// place files you want to import through the `$lib` alias in this folder.
export let czech = {
  primarySchool: "Základní škola",
  secondarySchool: "Střední škola",
  highSchool: "Vysoká škola",
  Individual: "Individuální", 
  Group: "Skupina",
  All: "Všechny"
}

export function humanReadableTime(minutes) {
  const hours = (minutes / 60)^0;
  const remainingMinutes = minutes % 60;
  return `${hours}h ${remainingMinutes}min`;
}