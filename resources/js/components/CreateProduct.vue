<template>
    <section>
        <div class="row">
            <div class="col-md-6">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="form-group">
                            <label for="">Product Name</label>
                            <input type="text" v-model="product_name" placeholder="Product Name" class="form-control">
                            <p v-if="validation_errors.title" :class="['text-danger']">{{ validation_errors.title[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Product SKU</label>
                            <input type="text" v-model="product_sku" placeholder="Product Name" class="form-control">
                            <p v-if="validation_errors.sku" :class="['text-danger']">{{ validation_errors.sku[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea v-model="description" id="" cols="30" rows="4" class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Media</h6>
                    </div>
                    <div class="card-body border">
                        <vue-dropzone ref="myVueDropzone" id="dropzone" :options="dropzoneOptions"  @vdropzone-complete="vdropzone_complete" @vdropzone-removed-file="vdropzone_removed_file"></vue-dropzone>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card shadow mb-4">
                    
                   
                    <div class="card-header text-uppercase">Preview</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <td>Variant</td>
                                    <td>Price</td>
                                    <td>Stock</td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(variant_price,index) in product_variant_prices" :key="index">
                                    <td>
                                        <div>
                                            <select v-model="variant_price.variant_id_one">
                                                <option v-for="variant in variants"
                                                        :value="variant.id">
                                                    {{ variant.title }}
                                                </option>
                                            </select>
                                            <select v-model="variant_price.product_variant_one">
                                                <option v-for="variant_d in variant_details"
                                                        :value="variant_d.id">
                                                    {{ variant_d.variant }}
                                                </option>
                                            </select>                                            
                                        </div>
                                        <br/>
                                        <div>
                                            <select v-model="variant_price.variant_id_two">
                                                <option v-for="variant in variants"
                                                        :value="variant.id">
                                                   {{ variant.title }}
                                                </option>
                                            </select>
                                             
                                            <select v-model="variant_price.product_variant_two">
                                                <option v-for="variant_d in variant_details"
                                                        :value="variant_d.id">
                                                   {{ variant_d.variant }}
                                                </option>
                                            </select>  
                                        </div>
                                        <br/>
                                        <div>
                                            <select v-model="variant_price.variant_id_three">
                                                <option v-for="variant in variants"
                                                        :value="variant.id">
                                                    {{ variant.title }}
                                                </option>
                                            </select>
                                            <select v-model="variant_price.product_variant_three">
                                                <option v-for="variant_d in variant_details"
                                                        :value="variant_d.id">
                                                    {{ variant_d.variant }}
                                                </option>
                                            </select>  
                                        </div>                                       
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.price">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="variant_price.stock">
                                    </td>
                                    <td>
                                        <button @click="delVarient(index)">x</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <div>   
                                <button @click="addVarient" class="btn btn-info">Add Varients</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button @click="saveProduct" type="submit" class="btn btn-lg btn-primary">Save</button>
        <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
    </section>
</template>

<script>
import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'
import InputTag from 'vue-input-tag'

export default {
    components: {
        vueDropzone: vue2Dropzone,
        InputTag
    },
    props: {
        base_url:{
            type: String,
            required: true
        },
        variants: {
            type: Array,
            required: true
        },
         variant_details:{
            type: Object,
            required:true
        }
    },
    data() {
        return {
            product_name: '',
            product_sku: '',
            description: '',
            images: [],
            product_variant: [
                {
                    option: this.variants[0].id,
                    tags: []
                }
            ],
            product_variant_prices: [],
            dropzoneOptions: {
                url: this.base_url.url+'/product-image-upload',
                thumbnailWidth: 150,
                maxFilesize: 0.5,
                headers: {
                    "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
                },
                addRemoveLinks: true
            },
            validation_errors:[]
        }
    },
    methods: {
        vdropzone_complete(response) {
            if(response && response.xhr.response){
                this.images.push(response.xhr.response)
            }
        },
        vdropzone_removed_file(a,b,c){
            var image_name = '';
            if(a.xhr){
                image_name = a.xhr.response;
            }else if(a.name){
                image_name = a.name;
            }
            if(image_name){
                var index = this.images.indexOf(image_name);
                this.images.splice(index, 1);
            }
        },
        // it will push a new object into product variant
        newVariant() {
            let all_variants = this.variants.map(el => el.id)
            let selected_variants = this.product_variant.map(el => el.option);
            let available_variants = all_variants.filter(entry1 => !selected_variants.some(entry2 => entry1 == entry2))
            // console.log(available_variants)

            this.product_variant.push({
                option: available_variants[0],
                tags: []
            })
        },

        // check the variant and render all the combination
        checkVariant() {
            let tags = [];
            this.product_variant_prices = [];
            this.product_variant.filter((item) => {
                tags.push(item.tags);
            })

            this.getCombn(tags).forEach(item => {
                this.product_variant_prices.push({
                    title: item,
                    price: 0,
                    stock: 0
                })
            })
        },

        // combination algorithm
        getCombn(arr, pre) {
            pre = pre || '';
            if (!arr.length) {
                return pre;
            }
            let self = this;
            let ans = arr[0].reduce(function (ans, value) {
                return ans.concat(self.getCombn(arr.slice(1), pre + value + '/'));
            }, []);
            return ans;
        },

        // store product into database
        saveProduct() {
            let product = {
                title: this.product_name,
                sku: this.product_sku,
                description: this.description,
                product_image: this.images,
                product_variant: this.product_variant,
                product_variant_prices: this.product_variant_prices
            }
            axios.post(this.base_url.url+'/product', product).then(response => {
                console.log(response.data);
                if(response.data==1){
                    window.location.href=this.base_url.url+'/product';
                 }
            }).catch(error => {
                if(error.response && error.response.data &&  error.response.data.errors){
                    this.validation_errors = error.response.data.errors
                }
                
            })
        },
        addVarient(){
            this.product_variant_prices.push({
                price: '',
                product_variant_one: '',
                product_variant_three: '',
                product_variant_two: '',
                stock: '',
                variant_id_one: '',
                variant_id_three: '',
                variant_id_two: ''
            });
        },


    },
    mounted() {
        //console.log('Component mounted.')
        setTimeout(function(){console.clear()},1000);
    }
}
</script>
