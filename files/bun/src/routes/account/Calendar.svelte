<script>
  export let calendarData = [];

  // the calendarData is in format [{"yyyy-mm-dd":[{event1},{event2}]},...]
  let days = [];
  // fill in days that are not in calendarData and put them in days
  $: calendarData && fillDays();
  function fillDays() {
    for (let i = 0; i < 7; i++) {
      let date = new Date();
      date.setDate(date.getDate() + i);
      let dateStr = date.toISOString().split('T')[0];
      if (!calendarData[dateStr]) {
        calendarData[dateStr] = [];
      }
      days.push({date: dateStr, events: calendarData[dateStr]});
    }
    console.log(days);
  }
</script>