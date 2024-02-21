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