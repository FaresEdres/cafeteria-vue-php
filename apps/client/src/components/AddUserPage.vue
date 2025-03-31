<template>
  <form>
    <div class="row mb-3">
      <label for="name" class="col-sm-2 col-form-label">Name</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="name">
      </div>
    </div>

    <div class="row mb-3">
      <label for="email" class="col-sm-2 col-form-label">Email address</label>
      <div class="col-sm-10">
        <input type="email" class="form-control" id="email">
      </div>
    </div>

    <div class="row mb-3">
      <label for="password" class="col-sm-2 col-form-label">Password</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="password">
      </div>
    </div>

    <div class="row mb-3">
      <label for="confirmPassword" class="col-sm-2 col-form-label">Confirm Password</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" id="confirmPassword">
      </div>
    </div>

    <div class="row mb-3">
      <label for="roomNumber" class="col-sm-2 col-form-label">Room No.</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="roomNumber">
      </div>
    </div>

    <div class="row mb-3">
      <label for="ext" class="col-sm-2 col-form-label">Extension</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" id="ext">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-sm-2"></div>
      <div class="col-sm-10">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="reset" class="btn btn-secondary">Reset</button>
      </div>
    </div>
  </form>
</template>

<script>
export default {

}
</script>

<style>

</style>
