<template>
  <div class="container">
      <!-- Title -->
        <h2 class="board-title">Kanban Board</h2>
        <!-- Export -->
        <div class="exports">
            <button type="button" class="export" @click="exportBoard"> Export </button>
        </div>
        <!-- Board -->
        <div class="board">
            <!-- Columns -->
            <div class="card" v-for="(column, index) in columns" :key="index">
                <div class="card__content">
                    <!-- Card Header -->
                    <div class="card__header">
                        <h3 class="card__title">{{ column.title }}</h3>
                        <button class="card__remove-column" @click="removeColumn(column.id)">Remove</button>
                    </div>
                    <!-- Cards -->
                    <ul class="card__list">
                        <draggable :list="column.cards" group="column" @change="changedCard($event, column.id)">
                            <li class="card__item" v-for="(card, i) in column.cards" :key="i">
                                <span class="card__item-title" @click="openCardModal(card)">{{ card.title }}</span>
                                <div class="card__item-action">
                                    <span class="card__item-action-up" v-if="i > 0" @click="moveUp(card, column.id)"><i class="fas fa-arrow-up"></i></span>
                                    <span class="card__item-action-down" v-if="i < (column.cards.length - 1)" @click="moveDown(card, column.id)"><i class="fas fa-arrow-down"></i></span>
                                    <span class="card__item-action-left" v-if="index > 0" @click="moveLeft(index, card)"><i class="fas fa-arrow-left"></i></span>
                                    <span class="card__item-action-right" v-if="index < (columns.length -1)" @click="moveRight(index, card)"><i class="fas fa-arrow-right"></i></span>
                                </div>
                            </li>
                        </draggable>
                    </ul>
                    <!-- Add New Card -->
                    <div class="card__new-item">
                        <div class="card__new-item-form" v-if="column.isOpenCardForm">
                            <!-- New Card Form -->
                            <input type="text" class="card__item-name" v-model="column.cardTitle" />
                            <textarea type="text" class="card__item-description" v-model="column.cardDescription"></textarea>
                            <input type="submit" class="card__item-submit" @click="saveCard(column)" value="Add Card" />
                            <a href="#" class="card__item-close" @click.prevent="column.isOpenCardForm = false">x</a>
                        </div>
                        <!-- Open new Card form -->
                        <button class="card__add-btn" v-if="!column.isOpenCardForm" @click="openCardForm(index)"><i class="fas fa-plus"></i> Add Card</button>
                    </div>
                </div>
            </div>
            <!-- Add New Column -->
            <div class="card" >
                <div class="card__new-column" :class="isOpenColumnForm ? 'card__new-column--active' : ''">
                    <!-- New column form -->
                    <div class="card__new-item-form card__column-form" v-if="isOpenColumnForm">
                        <input type="text" class="card__item-name" @keyup.enter="saveColumn" v-model="title" />
                        <input type="submit" class="card__item-submit card__new-column--submit" @click="saveColumn" value="Save Column" />
                        <a href="#" class="card__item-close" @click.prevent="isOpenColumnForm = false">x</a>
                    </div>
                    <!-- Open new column form -->
                    <button class="card__add-column-btn" v-if="!isOpenColumnForm" @click="isOpenColumnForm = true"><i class="fas fa-plus"></i> Add Column</button>
                </div>
            </div>
        </div>
        <!-- Update Card Modal -->
        <modal name="card-modal">
            <form class="modal">
                <!-- Title -->
                <div class="modal__input-group">
                    <label for="title" class="modal__input-group-label">Title</label>
                    <input type="text" id="title" class="modal__title modal__input-group-input" v-model="card.title" />
                </div>
                <!-- Description -->
                <div class="modal__input-group">
                    <label for="description" class="modal__input-group-label">Description</label>
                    <textarea type="text" id="description" class="modal__description modal__input-group-input" v-model="card.description"></textarea>
                </div>
                <!-- Submit Button -->
                <div class="card-input-group">
                    <input type="submit" id="submit" class="modal__submit" @click.prevent="updateCard(card)" value="Update Card"/>
                    <input type="button" id="close-modal" class="modal__close" @click="$modal.hide('card-modal')" value="Close"/>
                </div>
            </form>
        </modal>
  </div>
</template>

<script>
    import draggable from 'vuedraggable';
  export default {
      name: "kanban-board",
      data() {
        return {
            columns: [],
            isOpenColumnForm: false,
            title: '',
            card: {
                title: '',
                description: ''
            },
          };
        },
      components: {
          draggable
      },
    mounted() {
      this.fetchData()
    },
    methods: {
        async fetchData() {
            await window.axios.get('/api/columns').then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.columns = res.data.data.map(column => {
                        column.isOpenCardForm = false
                        column.cardTitle = ''
                        column.cardDescription = ''
                        return column
                    })
                }
            });
        },
        async saveColumn () {
            if (this.title.length < 1) {
                alert('Title must be greater than 1 charecter.')
                return;
            } 
            let data = {
                'title' : this.title
            }
            window.axios.post('/api/columns', data).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.title = ''
                    this.fetchData()
                }
            })
        },
        async removeColumn(id) {
            if (confirm('Are you sure you want to delete this column? This action will delete this column and related cards as well.'))
            window.axios.delete(`api/columns/${id}`).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.fetchData()
                }
            })
        },
        openCardForm: function (index) {
            // console.log(index)
            this.columns = this.columns.map((column, key) => {
                column.cardTitle = ''
                column.cardDescription = ''
                column.isOpenCardForm = false
                if (index === key) {
                    column.isOpenCardForm = true
                }
                return column
            })
        },
        openCardModal: function (card) {
            this.card = card
            this.$modal.show('card-modal')
        },
        async saveCard(column) {
            // console.log(column)
            let data = {
                title: column.cardTitle,
                description: column.cardDescription,
                column_id: column.id
            }

            await window.axios.post('/api/cards', data).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.fetchData()
                }
            })
        },
        async updateCard(card) {
            let data = {
                column_id : card.column_id,
                card_id: card.id,
                title: card.title,
                description: card.description
            }

            window.axios.put(`/api/cards/${card.id}`, data).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.fetchData()
                    this.$modal.hide('card-modal')
                }
            })
        },
        moveUpdate: function(data) {
            window.axios.post(`/api/cards/swap`, data).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.fetchData()
                }
            })
        },
        sortCards: function(column_id) {
            window.axios.post(`/api/cards/sort`, {column_id: column_id, sort: 'ASC'}).then(res => {
                // console.log(res)
                if (res.data.status === 'success') {
                    this.fetchData()
                }
            })
        },
        changedCard: function(event, column_id) {

            // console.log(event)
            
            if (event.added) {
                // console.log(event)
                let order = parseInt(event.added.newIndex) + 1;
                let card = event.added.element;

                let data = {
                    column_id : column_id,
                    card_id: card.id,
                    title: card.title,
                    description: card.description,
                    order: order,
                }

                // console.log(data);

                this.moveUpdate(data);
            }

            if(event.moved) {
                let order = parseInt(event.moved.newIndex) + 1;
                let card = event.moved.element;

                let data = {
                    column_id : column_id,
                    card_id: card.id,
                    order: order,
                }

                // console.log(data);

                this.moveUpdate(data);
            }

            if (event.removed) {
                this.sortCards(column_id);
            }
            
        },
        moveUp: function(card, column_id) {
            let data = {
                column_id : column_id,
                card_id: card.id,
                order: card.order - 1,
            }

            // console.log(data);
            this.moveUpdate(data);
        },
        moveDown: function(card, column_id) {
            let data = {
                column_id : column_id,
                card_id: card.id,
                order: card.order + 1,
            }

            // console.log(data);

            this.moveUpdate(data);
        },
        moveLeft: function(index, card) {
            let column = this.columns[(index - 1)];
            let prevColumn = this.columns[index];

            let data = {
                column_id : column.id,
                card_id: card.id,
                order: column.cards.length + 1,
            }

            this.moveUpdate(data);
            this.sortCards(prevColumn.id);
        },
        moveRight: function(index, card) {
            let column = this.columns[(index + 1)];
            let prevColumn = this.columns[index];

            let data = {
                column_id : column.id,
                card_id: card.id,
                order: column.cards.length + 1,
            }

            this.moveUpdate(data);
            this.sortCards(prevColumn.id);
        },
        async exportBoard() {
            // console.log('export')
            window.axios.post('/api/cards/export').then((res) => {
                // console.log(res)
                if (res.data.status === 'success') {
                    var fileURL = window.URL.createObjectURL(new Blob([res.data.data]));
                    var fileLink = document.createElement('a');

                    fileLink.href = fileURL;
                    fileLink.setAttribute('download', 'board.sql');
                    document.body.appendChild(fileLink);

                    fileLink.click();
                }
            });
        }
    }
  };
</script>

<style lang="scss">
    *, ::after, ::before {
        box-sizing: border-box;
    }
    body {
        padding: 0px;
        margin: 0px;
    }
    body {
        background-image: url('/images/board-bg.jpeg');
        background-position: center;
        background-size: cover;
        height: 100vh;
        background-attachment: fixed;
        overflow: hidden;
    }
    .container {
        width: 100%;
        padding-right: 15px;
        padding-left: 15px;
        margin-right: auto;
        margin-left: auto;
    }
    .board-title {
        text-align: center;
        padding: 50px 0px;
        margin: 0px;
        color: #222;
        font-weight: bold;
    }
    .exports {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 15px;
    }
    .export {
        background: #0079bf;
        box-shadow: unset;
        outline: 0;
        border: 0;
        border-radius: 4px;
        color: #fff;
        padding: 12px 15px;
        margin-bottom: 15px;
        cursor: pointer;
    }
    .board {
        position: absolute;
        z-index: 999;
        width: 100%;
        height: 100%;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: nowrap;
        flex-wrap: nowrap;
        overflow: scroll;
    }
    .card {
        -webkit-box-flex: 0;
        -ms-flex: 0 0 272px;
        flex: 0 0 272px;
        max-width: 272px;
        position: relative;
        height: 100%;
        min-height: 1px;
        padding-right: 5px;
        padding-left: 5px;
        vertical-align: top;
        white-space: nowrap;

        &__content {
            background-color: #ebecf0;
            border-radius: 3px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            max-height: 100%;
            position: relative;
            white-space: normal;
        }

        &__header {
            flex: 0 0 auto;
            padding: 10px 8px;
            position: relative;
            min-height: 20px;
            display: flex;
        }

        &__title {
            color: #000;
            flex: 1;
            margin: 0;
        }

        &__remove-column {
            border: 0;
            cursor: pointer;
        }

        &__list {
            list-style-type: none;
            padding: 0px 15px;
        }

        &__item {
            background-color: #fff;
            border-radius: 3px;
            box-shadow: 0 1px 0 rgba(9, 30, 66, .25) rgba(9, 30, 66, .08);
            display: block;
            padding: 20px 10px;
            margin-bottom: 8px;
            position: relative;
            text-decoration: none;
            display: flex;
        }

        &__item-title {
            flex: 1;
            cursor: pointer;
        }

        &__item-action {
            width: 50px;
            position: relative;
        }
        &__item-action-up {
            position: absolute;
            top: -16px;
            left: 20px;
            cursor: pointer;
        }
        &__item-action-down {
            position: absolute;
            bottom: -15px;
            left: 20px;
            cursor: pointer;
        }
        &__item-action-left {
            position: absolute;
            cursor: pointer;
        }
        &__item-action-right {
            position: absolute;
            right: 0px;
            cursor: pointer;
        }

        &__new-item {
            display: flex;
            justify-content: center;
            padding: 15px 0px;
        }
        &__add-btn {
            cursor: pointer;
            border: 0;
            color: #5e6c84;
            background: transparent;
        }

        &__new-item-form {
            padding: 0px 15px;
            width: 100%;
        }

        &__item-name {
            display: block;
            width: 100%;
            min-height: 40px;
            margin-bottom: 5px;
        }

        &__item-description {
            display: block;
            width: 100%;
            min-height: 80px;
            margin-bottom: 10px;
        }

        &__item-submit {
            padding: 10px;
            cursor: pointer;
        }

        &__item-close {
            margin-left: 10px;
            font-size: 25px;
            text-decoration: none;
            color: #6b778c;
        }

        &__new-column {
            background-color: hsla(0,0%,100%,.24);
            border-radius: 3px;
            min-height: 32px;

            &--active {
                background: #ebecf0;
            }

            &--submit {
                background: #0079bf;
                box-shadow: unset;
                outline: 0;
                border: 0;
                border-radius: 4px;
                color: #fff;
            }
        }
        &__column-form {
            padding: 10px;
        }
        &__add-column-btn {
            width: 100%;
            cursor: pointer;
            border: 0;
            padding: 15px 30px;
            transition: color 85ms ease-in;
            color: #fff;
            font-weight: bold;
            background: transparent;
        }
    }



    .modal {
        padding: 15px;
        height: 100%;
        background: #f4f5f7;

        &__input-group {
            margin-bottom: 15px;
        }

        &__input-group-label {
            margin-bottom: 15px;
            display: block;
            width: 100%;
        }

        &__input-group-input {
            margin-bottom: 15px;
            display: block;
            width: 100%;
        }
        &__title,
        &__description {
            background-color: #fff;
            border-radius: 3px;
            box-shadow: 0 1px 2px -1px rgba(9, 30, 66, .25), 0 0 0 1px rgba(9, 30, 66, .08);
            padding: 8px 12px;
            transition: box-shadow 85ms ease;
            border: 0;
            min-height: 40px;
        }
        &__description {
            min-height: 85px;
        }
        &__submit,
        &__close {
            background: #0079bf;
            box-shadow: unset;
            outline: 0;
            border: 0;
            border-radius: 4px;
            color: #fff;
            padding: 14px 15px;
            cursor: pointer;
        }

        &__close {
            background: red;
            border: 1px solid red;
        }
    }
</style>