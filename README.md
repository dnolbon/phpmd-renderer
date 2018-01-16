# Custom renderers for phpmd

## Usage

### JsonRenderer

```
phpmd path-to-source JsonRenderer codesize,unusedcode,naming
```

This works only with custom phpmd build <https://github.com/dnolbon/phpmd>

```
phpmd --custom-pretty-json 1 path-to-source JsonRenderer codesize,unusedcode,naming 
```

### MongoDbRenderer

This works only with custom phpmd build <https://github.com/dnolbon/phpmd>

```
phpmd --custom-mongo-uri mongodb://127.0.0.1:27017 --custom-mongo-namespace phpmd.runs path-to-source MongoDbRenderer codesize,unusedcode,naming
```