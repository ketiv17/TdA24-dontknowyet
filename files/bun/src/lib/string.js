// place files you want to import through the `$lib` alias in this folder.

export function fullName (lecturer = {}) {
  let rtn = "";
  ["title_before", "first_name", "middle_name", "last_name", "title_after"].forEach((word) => {
    rtn += " "+null2string(lecturer[word]);
  });
  return rtn.trimStart();
}

export function null2string (string) {
  if (string === null) {
    return "";
  }
  return string;
}

export function formatDate(dateStr) {
  let date = new Date(dateStr);
  let day = String(date.getDate()).padStart(2, '0');
  let month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based in JavaScript
  let year = date.getFullYear();

  return `${day}. ${month}. ${year}`;
}

export function shortenString (string, width) {
  let lenght = ((width - 6) / 9) ^ 0; // ^0 rounds the number down
  if (string.length > lenght) {
    return string.slice(0,lenght - 2).concat("...");
  }
  return string;
}