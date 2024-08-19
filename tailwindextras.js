tailwind.config = {
  darkMode: 'selector', // or 'media' if you want it to follow the user's system preference
    theme: {
      extend: {
        colors: {
          coral: '#FF6F61',
          darkslate: '#3A3A3A',
          golden: '#FFD700',
          lightgray: '#F5F5F5',
          almostblack: '#212121',
          coralhov: '#E16B4E'
        },
      },
    },
    variants: {
      extend: {
        backgroundColor: ['dark'],
        textColor: ['dark'],
        borderColor: ['dark'],
        placeholderColor: ['dark'],
      },
    },
    plugins: [],
  };
  