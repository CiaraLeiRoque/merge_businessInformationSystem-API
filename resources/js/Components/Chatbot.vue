<template>
  <div id="chatbot">
    <!-- The chat circle and "Chat with us!" bubble -->
    <div v-if="chatbotImageUrl && !chatExpanded" class="chat-container">
      <div @click="expandChat" class="chat-circle">
        <img :src="chatbotImageUrl" alt="Chat" class="chat-icon" />
      </div>
      <transition name="fade">
        <div v-if="showBubble" class="chat-bubble">Chat with us!</div>
      </transition>
    </div>

    <!-- Expanded chat window -->
    <div v-if="chatExpanded" class="chat-window">
      <div class="chat-header">
        <span>Chatbot</span>
        <button @click="collapseChat">X</button>
      </div>

      <div class="chat-body">
        <div ref="messages" class="messages">
          <transition-group name="message" tag="div">
            <div
              v-for="message in messages"
              :key="message.id"
              :class="['message', message.sender]"
            >
              <div v-html="message.text"></div>
              <div v-if="message.buttons && message.sender === 'bot'" class="chat-buttons">
                <button
                  v-for="button in message.buttons"
                  :key="button.id"
                  @click="handleButtonClick(button)"
                  :disabled="buttonsDisabled"
                >
                  {{ button.text }}
                </button>
              </div>
            </div>
          </transition-group>

          <div v-if="isTyping" class="message bot typing">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  data() {
    return {
      chatExpanded: false,
      messages: [],
      isTyping: false,
      businessName: '',
      chatInitialized: false,
      chatbotImageUrl: '',
      buttonsDisabled: false, 
      showBubble: true, 
      bubbleTimer: null, 
    };
  },
  mounted() {
    this.preloadBusinessData();
    this.startBubbleAnimation();
  },
  beforeUnmount() {
    clearInterval(this.bubbleTimer);
  },
    methods: {
    expandChat() {
      this.chatExpanded = true;
      if (!this.chatInitialized) {
        this.initializeChat();
        this.chatInitialized = true;
      }
    },
    collapseChat() {
      this.chatExpanded = false;
    },
    async preloadBusinessData() {
      try {
        const response = await axios.get('/api/Business');
        
        // Check if the response contains any data
        if (response.data) {
          // Safely check for the business data structure
          const businessData = response.data[0] || {}; // Fallback to an empty object if no data
          const businessImage = businessData.business_image || ''; // Check if image exists

          // Set business details with default values if fields are missing
          this.businessName = businessData.business_Name || 'Unknown Business';
          this.businessProvince = businessData.business_Province || 'Unknown Province';
          this.businessCity = businessData.business_City || 'Unknown City';
          this.businessBarangay = businessData.business_Barangay || 'Unknown Barangay';
          this.businessAddress = businessData.business_Address || 'Unknown Address';
          this.businessPhoneNumber = businessData.business_Phone_Number || 'Unknown Phone Number';
          this.businessTelephoneNumber = businessData.business_Telephone_Number || 'Unknown Phone Number';
          this.businessFacebook = businessData.business_Facebook || 'Unknown Facebook';
          this.businessX = businessData.business_X || 'Unknown X';
          this.businessInstagram = businessData.business_Instagram || 'Unknown Instagram ';
          this.businessTiktok = businessData.business_Tiktok || 'Unknown Tiktok  ';


          // Set the chatbot image URL, or use a default image
          this.chatbotImageUrl = businessImage 
            ? `/storage/business_logos/${businessImage}` 
            : '/default_image_path.png';
        } else {
          console.error("No business data found.");
        }

        // Fetch chatbot response data
        const chatbotResponse = await axios.get('/api/chatbot-response'); // Example endpoint
        if (chatbotResponse.data && chatbotResponse.data.length > 0) {
          const firstResponse = chatbotResponse.data[0]; // Get the first chatbot response
          this.workingHours = firstResponse?.chabot_BWHours || 'Unavailable';
          this.productDescription = firstResponse?.chabot_BPDescription || 'Unavailable';
          this.lazada = firstResponse?.chabot_Lazada || 'Lazada Link unavailable';
          this.shopee = firstResponse?.chabot_Shopee || 'Shopee Link unavailable';
          this.region1 = firstResponse.chabot_Region1 || 'Delivery time for Region 1 unavailable';
          this.region2 = firstResponse.chabot_Region2 || 'Delivery time for Region 2 unavailable';
          this.region3 = firstResponse.chabot_Region3 || 'Delivery time for Region 3 unavailable';
          this.region4a = firstResponse.chabot_Region4A || 'Delivery time for Region 4A unavailable';
          this.region4b = firstResponse.chabot_Region4B || 'Delivery time for Region 4B unavailable';
          this.region5 = firstResponse.chabot_Region5 || 'Delivery time for Region 5 unavailable';
          this.regionNCR = firstResponse.chabot_NCR || 'Delivery time for NCR unavailable';
          this.regionCAR = firstResponse.chabot_CAR || 'Delivery time for CAR unavailable';


          
        } else {
          console.error("No chatbot response data found.");
        }

      } catch (error) {
        console.error("Error fetching business or chatbot response data:", error);
      }
    },
    startBubbleAnimation() {
      // Show bubble for 3 seconds, hide for 15 seconds, and repeat
      this.bubbleTimer = setInterval(() => {
        this.showBubble = true;
        setTimeout(() => {
          this.showBubble = false;
        }, 3000); // Hide after 3 seconds
      }, 18000); // 3 seconds bubble + 15 seconds delay
    },
    async initializeChat() {
      if (!this.chatbotImageUrl) {
        await this.preloadBusinessData();
      }
      this.addInitialChatMessage();
    },
    addInitialChatMessage() {
      this.messages.push({
        id: new Date().getTime(),
        text: `Hi! Welcome to ${this.businessName}. How can we help you today?`,
        sender: 'bot',
        buttons: [
          { id: 1, text: "Business Details" },
          { id: 2, text: "Regional Delivery Times" },
        ]
      });
    },
    showEndMessage() {
      this.messages.push({
        id: new Date().getTime(),
        text: 'Is there anything else you would like to know?',
        sender: 'bot',
        buttons: [
          { id: 1, text: "Business Details" },
          { id: 2, text: "Regional Delivery Times" },
        ]
      });
    },

    handleButtonClick(button) {
      if (this.buttonsDisabled) return; // Prevent multiple clicks while disabled
      
      this.buttonsDisabled = true; // Disable buttons when one is clicked

      this.messages.push({ id: new Date().getTime(), text: `You clicked ${button.text}`, sender: 'user' });
      this.scrollToBottom();
      this.isTyping = true;
      
      setTimeout(() => {
        this.isTyping = false;
        let botResponse = '';
            
        switch (button.id) {
          case 1:
            this.messages.push({
              id: new Date().getTime(),
              text: `What would you like to know? `,
              sender: 'bot',
              buttons: [
                { id: 3, text: "Location" },
                { id: 4, text: "Contact" },
                { id: 5, text: "Working Hours"},
                { id: 6, text: "Products"},
              ],
              buttonContainerClass: 'chat-buttons-container',
            });
            break;
            
          case 2:
          this.messages.push({
              id: new Date().getTime(),
              text: 'Please Select a Region:',
              sender: 'bot',
              buttons: [
                { id: 10, text: "Region 1" },
                { id: 11, text: "Region 2" },
                { id: 12, text: "Region 3 " },
                { id: 13, text: "Region 4A" },
                { id: 14, text: "Region 4B" },
                { id: 15, text: "Region 5" },
                { id: 16, text: "Region NCR" },
                { id: 17, text: "Region CAR" }
              ]
            });

          
            break;
            
            case 3:
            const address = `${this.businessAddress}, ${this.businessBarangay}, ${this.businessCity}, ${this.businessProvince}`;
            const encodedAddress = encodeURIComponent(address);

            // Request user's current location
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const userLat = position.coords.latitude;
                    const userLng = position.coords.longitude;

                    // Create a Google Maps directions URL
                    const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${encodedAddress}&origin=${userLat},${userLng}`;

                    // Push the message with the Google Maps link
                    this.messages.push({
                        id: new Date().getTime(),
                        text: `We are located at ${address}. \n <a href="${googleMapsUrl}" target="_blank" style="color: blue; text-decoration: underline;">Click here</a> to view us on Google Maps and get directions from your current location.`,
                        sender: 'bot',
                    });

                    setTimeout(() => {
                        this.showEndMessage();
                        this.scrollToBottom();
                    }, 1000);
                },
                (error) => {
                    // Fallback in case geolocation fails
                    console.error("Geolocation error:", error);

                    this.messages.push({
                        id: new Date().getTime(),
                        text: `We are located at ${address}. Unfortunately, we couldn't get your current location. \n <a href="https://www.google.com/maps/search/?api=1&query=${encodedAddress}" target="_blank" style="color: blue; text-decoration: underline;">Click here</a> to view us on Google Maps.`,
                        sender: 'bot',
                    });

                    setTimeout(() => {
                        this.showEndMessage();
                        this.scrollToBottom();
                    }, 1000);
                }
            );

            break;


            
          case 4:
          this.messages.push({
              id: new Date().getTime(),
              text: 'Select a Contact Option',
              sender: 'bot',
              buttons: [
                { id: 7, text: "Contact Number" },
                { id: 8, text: "Social Media" },
                { id: 9, text: "E-Commerce" },
              ]
            });

          
            break;

            case 5:
            let messageText;

            if (this.workingHours === 'Unavailable') {
              messageText = "The working hours are currently unavailable.";
            } else {
              messageText = `We are available from ${this.workingHours}`;
            }

            this.messages.push({
              id: new Date().getTime(),
              text: messageText,
              sender: 'bot',
            });

            setTimeout(() => {
              this.showEndMessage();
              this.scrollToBottom();
            }, 1000);

            break;

          
            case 6:
            let productMessage;

            if (this.productDescription === 'Unavailable' || !this.productDescription) {
                productMessage = "Sorry, the product list is currently unavailable.";
            } else {
                productMessage = `We sell ${this.productDescription} \n <a href="http://127.0.0.1:8000/products_page" target="_blank" style="color: blue; text-decoration: underline;">Click here</a> to check out our products.`;
            }

            this.messages.push({
              id: new Date().getTime(),
              text: productMessage,
              sender: 'bot',
            });

            setTimeout(() => {
              this.showEndMessage();
              this.scrollToBottom();
            }, 1000);

            break;

          
          
          case 7:
          this.messages.push({
              id: new Date().getTime(),
              text:this.formatMessage( `You can contact us through the following Numbers: 
                    \n Phone number: ${this.businessPhoneNumber} 
                    \n Telephone number: ${this.businessTelephoneNumber} `),
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;

            case 8:
            this.messages.push({
              id: new Date().getTime(),
              text: this.formatMessage(`You can contact us through the following Social Media Sites: 
                <br> Facebook: <a href="https://${this.businessFacebook}" target="_blank" style="color: blue; text-decoration: underline;">${this.businessFacebook}</a> 
                <br> X: <a href="https://${this.businessX}" target="_blank" style="color: blue; text-decoration: underline;">${this.businessX}</a> 
                <br> Instagram: <a href="https://${this.businessInstagram}" target="_blank" style="color: blue; text-decoration: underline;">${this.businessInstagram}</a> 
                <br> Tiktok: <a href="https://${this.businessTiktok}" target="_blank" style="color: blue; text-decoration: underline;">${this.businessTiktok}</a>`),
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            break;


            case 9:
            this.messages.push({
              id: new Date().getTime(),
              text: this.formatMessage(`You can contact us through the following E-Commerce Sites: 
                <br> Lazada: <a href="https://${this.lazada}" target="_blank" style="color: blue; text-decoration: underline;">${this.lazada}</a> 
                <br> Shopee: <a href="https://${this.shopee}" target="_blank" style="color: blue; text-decoration: underline;">${this.shopee}</a>`),
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            break;


          
          case 10:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 1: ${this.region1}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;
            
          case 11:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 2: ${this.region2}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;
          
          case 12:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 3: ${this.region3}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;
          
          case 13:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 4A: ${this.region4a}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;
          
          case 14:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 4B: ${this.region4b}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;

          case 15:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for Region 5: ${this.region5}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;

          case 16:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for NCR Region: ${this.regionNCR}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;

          case 17:
          this.messages.push({
              id: new Date().getTime(),
              text: `Delivery time for CAR Region: ${this.regionCAR}  `,
              sender: 'bot',
            });
            setTimeout(() => {
              this.showEndMessage(); 
              this.scrollToBottom();
            }, 1000);
            
            break;

          default:
            botResponse = 'I\'m not sure what you selected!';
        }

        if (botResponse.trim()) {
          this.messages.push({ id: new Date().getTime(), text: botResponse, sender: 'bot' });
        }

        this.scrollToBottom();
        this.buttonsDisabled = false;
      }, 1000);
    },
    scrollToBottom() {
      this.$nextTick(() => {
        const messagesContainer = this.$refs.messages;
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      });
    },
    formatMessage(messageText) {
    return messageText.replace(/\n/g, '<br>'); 
  },

  startBubbleAnimation() {
      // Show bubble for 3 seconds, hide it for 15 seconds, and repeat
      this.bubbleTimer = setInterval(() => {
        this.showBubble = true;
        setTimeout(() => {
          this.showBubble = false;
        }, 2000); 
      }, 15000); 
    },
  },
};

</script>

<style scoped>
#chatbot {
  position: fixed;
  bottom: 20px;
  right: 20px;
  z-index: 10000;
}

.chat-circle {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  border-color: black;
  border: 2px solid black;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  overflow: hidden; 
}
/* Chat bubble */
.chat-container {
  position: relative;
}

.chat-bubble {
  position: absolute;
  left: -120px;
  top: 30%;
  background-color: #20242c;
  color: white;
  padding: 10px 15px;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  font-size: 14px;
  font-weight: bold;
  white-space: nowrap;
  opacity: 1;
  transition: opacity 0.5s ease;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.chat-icon {
  width: 100%; 
  height: 100%; 
  object-fit: cover; 
  border-radius: 50%; 
}

.chat-window {
  width: 300px;
  height: 400px;
  background-color: white;
  border: 1px solid #ccc;
  border-radius: 10px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  overflow: hidden; 
}

.chat-header {
  background-color: #20242c;
  color: white;
  padding: 5px 10px; 
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 40px; 
  border-top-left-radius: 10px;
  border-top-right-radius: 10px;
}

.chat-header button {
  background: none;
  border: none;
  color: white;
  font-size: 16px;
  cursor: pointer;
  outline: none; /* Removes outline */
  padding: 0 5px;
}

.chat-header button:hover {
  color: white; /* Add hover effect if desired */
}


.chat-body {
  flex: 1;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
}

.chat-body {
  flex: 1;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  overflow: hidden;
}

.messages {
  flex: 1;
  overflow-y: auto;
  max-height: 300px;
}

.message {
  padding: 5px 10px;
  margin-bottom: 10px;
  border-radius: 5px;
}

.message.bot {
  background-color: hsl(0, 100%, 100%);
  border: 1px solid #20242c;
  text-align: left;
}

.message.user {
  background-color: #20242c;
  color: white;
  text-align: right;
}
.chat-buttons-container {
  display: flex;
  flex-wrap: wrap;
  gap: 10px; 
  margin-top: 10px;
  justify-content: center;
}

.chat-button {
  padding: 10px 20px; 
  background-color: transparent;
  color: #20242c;
  border: 1px solid #20242c;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  min-width: 80px; 
  flex: 1 1 auto; 
  text-align: center;
  box-sizing: border-box;
}

.chat-button:hover {
  background-color: #20242c;
  color: white;
}

.chat-buttons button {
  padding: 10px 20px; 
  background-color: transparent;
  color: #20242c;
  border: 1px solid #20242c;
  border-radius: 20px;
  cursor: pointer;
  font-size: 14px;
  min-width: 100px; 
  margin: 5px; 
}


.chat-buttons button:hover {
  background-color: #20242c;
  color: white;
}

.typing {
  display: flex;
  gap: 5px;
}

.dot {
  width: 6px;
  height: 6px;
  background-color: #20242c;
  border-radius: 50%;
  animation: blink 1s infinite alternate;
}

.dot:nth-child(2) {
  animation-delay: 0.2s;
}

.dot:nth-child(3) {
  animation-delay: 0.4s;
}

@keyframes blink {
  from {
    opacity: 0.3;
  }
  to {
    opacity: 1;
  }
}
</style>
