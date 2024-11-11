    <template>
        <div>
            <div class="product-container px-36">
                <div class="product-details">
                    <div class="product-info">
                        <form @submit.prevent="checkout" class="order-form">
                            <button class="btn-submit" id="checkout-btn" type="submit">
                                <span class="icon-credit-card text-center">💳</span>Pagar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <script>
    export default {
        props: {
            mp: "TEST-3796733327633492-102515-7f4d7f0ab89b5f70facc784ce720fb04-1345692363",
        },
        methods: {
            checkout() {
                this.$inertia.post('/create-preference', {}, {
                    onSuccess: (response) => {
                        const preference = response.props.preference;
                        if (preference && preference.id) {
                            this.mp.checkout({
                                preference: {
                                    id: preference.id,
                                },
                                autoOpen: true,
                            });
                            console.log('Respuesta de la preferencia:', preference);
                        } else {
                            console.error('Error al obtener el ID de preferencia');
                        }
                    },
                    onError: (error) => {
                        console.error('Error al crear la preferencia:', error);
                    }
                });
            }
        }
    };
    </script>




    <style scoped>
    .product-container {
        /* Tus estilos */
    }

    .btn-submit {
        /* Tus estilos */
    }
    </style>
