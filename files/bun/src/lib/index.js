// place files you want to import through the `$lib` alias in this folder.
export function textContrast (colorhex) {
  colorhex = colorhex.replace("#", "");
  var r = parseInt(colorhex.substr(0,2),16);
  var g = parseInt(colorhex.substr(2,2),16);
  var b = parseInt(colorhex.substr(4,2),16);
  var yiq = ((r*299)+(g*587)+(b*114))/1000;
  return (yiq >= 128) ? '#333333' : 'white';
}

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