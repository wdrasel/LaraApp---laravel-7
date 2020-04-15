<template>
    <div class="container">
        <div class="row">
            <div class="col-md-12 mt-3">
            <!-- Widget: user widget style 1 -->
            <div class="card card-widget widget-user">
              <!-- Add the bg color to the header using any of the bg-* classes -->
              <div class="widget-user-header text-white" style="background: url('./img/user-cover.jpg') center center;">
                <h3 class="widget-user-username text-right">{{this.form.name}}</h3>
                <h5 class="widget-user-desc text-right">{{this.form.type}}</h5>
              </div>
              <div class="widget-user-image">
                <img class="img-circle" :src="getProfilePhoto()" alt="User Avatar">
              </div>
              <div class="card-footer">
                <div class="row">
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">3,200</h5>
                      <span class="description-text">SALES</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4 border-right">
                    <div class="description-block">
                      <h5 class="description-header">13,000</h5>
                      <span class="description-text">FOLLOWERS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                  <div class="col-sm-4">
                    <div class="description-block">
                      <h5 class="description-header">35</h5>
                      <span class="description-text">PRODUCTS</span>
                    </div>
                    <!-- /.description-block -->
                  </div>
                  <!-- /.col -->
                </div>
                <!-- /.row -->
              </div>
            </div>
            <!-- /.widget-user -->


            <div class="card">
              <div class="card-header p-2">
                <ul class="nav nav-pills">
                  <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Activity</a></li>
                  <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
              </div><!-- /.card-header -->
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane active" id="activity">
                   <h2>activety</h2>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="settings">
                    <form class="form-horizontal" _lpchecked="1">
                      <div class="form-group row">
                        <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                          <input v-model="form.name" type="email" class="form-control" id="inputName"
                          placeholder="Name" :class="{ 'is-invalid': form.errors.has('name') }">
                           <has-error :form="form" field="name"></has-error>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                          <input v-model="form.email" type="email" :class="{ 'is-invalid': form.errors.has('email') }"
                          class="form-control" id="inputEmail" placeholder="Email">
                           <has-error :form="form" field="email"></has-error>
                        </div>
                      </div>

                      <div class="form-group row">
                        <label for="inputExperience" class="col-sm-2 col-form-label">Experience</label>
                        <div class="col-sm-10">
                          <textarea v-model="form.bio" class="form-control" id="inputExperience"
                          placeholder="Experience" style="line-height: 24px;"></textarea>
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputSkills" class="col-sm-2 col-form-label">Profile Photo</label>
                        <div class="col-sm-10">
                          <input  type="file"  @change="updateProfile" name="photo" class="form-input">
                        </div>
                      </div>
                      <div class="form-group row">
                        <label for="inputEmail" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                          <input  v-model="form.password" type="password" class="form-control" id="inputPassword" placeholder="Password">
                        </div>
                      </div>
                      <div class="form-group row">
                        <div class="offset-sm-2 col-sm-10">
                          <button @click.prevent="updateInfo" type="submit" class="btn btn-success">Update</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
              </div><!-- /.card-body -->
            </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: "Profile",
        data(){
            return {
                form: new Form({
                    id: '',
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',
            })
            }  
        },
        mounted() {
            console.log('Component mounted')
        },

        methods:{
            getProfilePhoto(){

              let photo = (this.form.photo.length > 200 ) ? this.form.photo : "/img/profile/"+ this.form.photo ;
              return photo;
            },
            updateProfile(e){
                    let file = e.target.files[0];
                    if(file['size'] < 2111775 ){
                        let reader = new FileReader();
                    reader.onloadend = (file) => {
                    this.form.photo = reader.result;
                    }
                    reader.readAsDataURL(file);

                }else{
                        swal.fire({
                                icon: 'error',
                                title:'Oops...',
                                text:'You are uploading large file (max:2MB)'
                        });
                }
            },
            updateInfo(){
                this.$Progress.start();
                this.form.put('api/profile')
                .then(() => {

                    this.$Progress.finish()
                })
                .catch(()=>{
                    this.$Progress.fail();
                });
            },
        },

        created(){
            axios.get("api/profile")
            .then(({data}) => (this.form.fill(data)))
        }

    }
</script>

<style scoped>
    .widget-user-header{
        background-size:cover !important;
        background-position: center center;
    }
</style>
