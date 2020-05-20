# parchment

A download API.

## setup

`$ composer install`

Create a .env file with the following information in $PARCHMENT_ROOT_DIR:
```
APP_ENV=prod
PARCHMENT_CACHE=/path/to/a/cache/directory
PARCHMENT_DOWNLOADS=/path/to/file/storage/directory
APP_SECRET=some-secret-string
```

## adding builds

### Copy the file to the downloads folder
First, copy the file to the downloads folder. It needs to match the following pattern:    
`$PARCHMENT_DOWNLOADS/$project_name/$project_version/$build_number.jar`

As an example, if the project name is `paper`, the project version is `1.15.2`, and the build number is `244`, you would copy the file to `$PARCHMENT_DOWNLOADS/paper/1.15.2/244.jar`

### Tell Parchment there's a new build
Secondly, we need to inform Parchment about the new build. To do that we can use the `add-build` command.
```
$ php $PARCHMENT_ROOT_DIR/bin/console app:add-build $project_name $project_version $build_number
```

To build off our previous example, we'd execute
```
$ php $PARCHMENT_ROOT_DIR/bin/console app:add-build paper 1.15.2 644
```
