<script>
  export let calendarData = [];
  let days = {};

  // the calendarData is in format {"yyyy-mm-dd":[{event1},{event2}],...}
  // fill in days for the that are not in calendarData and put them in days
  $: calendarData && fillDays();
  function fillDays() {
    for (let i = 0; i < 28; i++) {
      let date = new Date();
      date.setDate(date.getDate() + i);
      let dateStr = date.toISOString().split('T')[0];
      if (!calendarData[dateStr]) {
        calendarData[dateStr] = [];
      }
      days.push({date: calendarData[dateStr]});
    }
    console.log(days);  
    console.log(currentDay);
  }
  // the current day of the week
  let currentDay = new Date().getDay();
</script>