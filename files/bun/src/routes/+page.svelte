<script>
  let messages = [];
  let newMessage = "";

  async function sendMessage() {
    if (newMessage.trim() !== "") {
      messages = [...messages, newMessage];
      let response = await fetch("/api/", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ message: newMessage }),
      });
      newMessage = await response.text();
      messages = [...messages, newMessage];
      newMessage = "";
    }
  }
</script>

<div class="flex flex-col h-screen">
  <div class="flex-grow border p-4">
    {#each messages as message}
      <div class="mb-2">{message}</div>
    {/each}
  </div>

  <div class="flex items-center border-t p-4">
    <input
      type="text"
      class="flex-grow border rounded px-2 py-1 mr-2 variant-filled-secondary"
      placeholder="Type your message..."
      bind:value={newMessage}
      on:keydown={(e) => e.key === "Enter" && sendMessage()}
    />

    <button
      class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded"
      on:click={sendMessage}
    >
      Send
    </button>
  </div>
</div>